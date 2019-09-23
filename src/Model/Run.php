<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Checker\CheckResult;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      subresourceOperations={
*           "api_sites_runs_get_subresource": {"normalization_context": {"groups": {"get_runs_for_site", "timestamp"}}}
 *      },
 *      collectionOperations={},
 *      itemOperations={"get"}
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\RunRepository")
 * @ORM\Table()
 */
class Run implements HasTimestamp
{
    use HasTimestampImpl;

    /**
     * @Groups({"get_runs_for_site", "get_run", "get_sites", "get_site"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @Groups({"get_run"})
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="runs")
     */
    private Site $site;

    /**
     * @Groups({"get_run"})
     * @ORM\Column(type="json_document", options={"jsonb": true})
     *
     * @var CheckResult[]
     */
    private array $checkResults = [];

    public function __construct(Site $site)
    {
        $this->id   = Uuid::uuid4();
        $this->site = $site;

        $this->initialize();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @return CheckResult[]
     */
    public function getCheckResults(): array
    {
        return $this->checkResults;
    }

    /**
     * @param CheckResult[] $checkResults
     */
    public function setCheckResults(array $checkResults): void
    {
        $this->checkResults = $checkResults;
    }

    /**
     * @Groups({"get_runs_for_site", "get_run", "get_sites", "get_site"})
     */
    public function getLowerResultLevel(): string
    {
        $level = 'success';

        foreach ($this->getCheckResults() as $checkResult) {
            if ('error' === $checkResult->getLevel()) {
                return 'error';
            }

            if ('warning' === $checkResult->getLevel()) {
                $level = 'warning';
            }
        }

        return $level;
    }

    public static function publish(Site $site, array $results): self
    {
        $run = new self($site);
        $run->setCheckResults($results);

        return $run;
    }
}

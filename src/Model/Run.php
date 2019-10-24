<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Checker\CheckResult;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      mercure=true,
 *      attributes={"order"={"createdAt": "DESC"}},
 *      normalizationContext={"groups": {"get_run", "timestamp"}},
 *      subresourceOperations={
 *           "api_sites_runs_get_subresource": {"normalization_context": {"groups": {"get_runs_for_site", "timestamp"}}}
 *      },
 *      collectionOperations={},
 *      itemOperations={
 *          "get": {"normalization_context": {"groups": {"get_run", "timestamp"}}}
 *      }
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
    private ?Site $site = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get_runs_for_site", "get_run", "get_sites", "get_site"})
     */
    private bool $running = false;

    /**
     * @ORM\OneToMany(targetEntity=RunCheckResult::class, mappedBy="run", cascade={"persist"})
     *
     * @var Collection<RunCheckResult>
     */
    private Collection $checkResults;

    public function __construct(Site $site)
    {
        $this->id           = Uuid::uuid4();
        $this->site         = $site;
        $this->checkResults = new ArrayCollection;

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
     * @return Collection<RunCheckResult>
     */
    public function getCheckResults(): Collection
    {
        return $this->checkResults;
    }

    public function addCheckResult(ConfiguredCheck $configuredCheck, CheckResult $checkResult): void
    {
        $this->checkResults->add(new RunCheckResult($this, $configuredCheck, $checkResult));
    }

    public function begin(): void
    {
        $this->running = true;
    }

    public function finish(): void
    {
        $this->running = false;
    }

    public function isRunning(): bool
    {
        return $this->running;
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

    public static function publish(Site $site): self
    {
        return new self($site);
    }
}

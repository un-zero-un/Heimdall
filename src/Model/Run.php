<?php

declare(strict_types=1);

namespace App\Model;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Checker\CheckResult;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\RunRepository")
 * @ORM\Table()
 */
class Run implements HasTimestamp
{
    use HasTimestampImpl;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class)
     */
    private Site $site;

    /**
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

    public static function publish(Site $site, array $results): self
    {
        $run = new self($site);
        $run->setCheckResults($results);

        return $run;
    }
}

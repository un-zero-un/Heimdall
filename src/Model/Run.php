<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Behavior\Equatable;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Checker\CheckResult;
use App\ValueObject\ResultLevel;
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
 *      collectionOperations={
 *           "get": {
 *               "normalization_context": {"groups": {"get_runs_for_site", "timestamp"}},
 *           }
 *     },
 *      itemOperations={
 *          "get": {"normalization_context": {"groups": {"get_run", "timestamp"}}}
 *      }
 * )
 * @ApiFilter(SearchFilter::class, properties={"site"})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\RunRepository")
 * @ORM\Table()
 */
class Run implements HasTimestamp, Equatable
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
     * @ORM\ManyToOne(targetEntity=Site::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Site $site;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get_runs_for_site", "get_run", "get_sites", "get_site"})
     */
    private bool $running = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"get_runs_for_site", "get_run", "get_sites", "get_site"})
     */
    private ?string $runResult = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"get_runs_for_site", "get_run", "get_sites", "get_site"})
     */
    private ?string $siteResult = null;

    /**
     * @ApiSubresource()
     * @ORM\OneToMany(targetEntity=RunCheckResult::class, mappedBy="run", cascade={"persist"})
     *
     * @var Collection<RunCheckResult>
     * @psalm-var Collection<int, RunCheckResult>
     */
    private Collection $checkResults;

    public function __construct(Site $site)
    {
        $this->id           = Uuid::uuid4();
        $this->checkResults = new ArrayCollection;
        $this->site         = $site;

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
     * @psalm-return Collection<int, RunCheckResult>
     */
    public function getCheckResults(): Collection
    {
        return $this->checkResults;
    }

    /**
     * @return RunCheckResult
     */
    public function addCheckResult(ConfiguredCheck $configuredCheck, CheckResult $checkResult): RunCheckResult
    {
        $runCheckResult = new RunCheckResult($this, $configuredCheck, $checkResult);
        $this->checkResults->add($runCheckResult);

        return $runCheckResult;
    }

    public function begin(): void
    {
        $this->running = true;
    }

    public function finish(string $siteResult): void
    {
        $this->siteResult = $siteResult;
        $this->running    = false;
        $this->runResult  = ResultLevel::findWorst(
            array_map(
                fn(RunCheckResult $checkResult) => ResultLevel::fromString($checkResult->getLevel()),
                $this->checkResults->toArray()
            )
        )->toString();
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    public function getRunResult(): ?string
    {
        return $this->runResult;
    }

    public function getSiteResult(): ?string
    {
        return $this->siteResult;
    }

    public function isEqualTo(Equatable $equatable): bool
    {
        if (!$equatable instanceof self) {
            return false;
        }

        return $this->getId()->equals($equatable->getId());
    }

    public static function publish(Site $site): self
    {
        return new self($site);
    }
}

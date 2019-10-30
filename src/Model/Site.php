<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Checker\CheckResult;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Util\Urlizer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      mercure=true,
 *      collectionOperations={"get": {"normalization_context": {"groups": {"get_sites", "timestamp"}}}},
 *      itemOperations={"get": {"normalization_context": {"groups": {"get_site", "timestamp"}}}}
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="site_slug_unique",columns={"slug"})},
 * )
 */
class Site implements HasTimestamp
{
    use HasTimestampImpl;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string")
     */
    private string $slug;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private string $url;

    /**
     * @ORM\OneToMany(targetEntity=ConfiguredCheck::class, mappedBy="site")
     *
     * @var Collection<ConfiguredCheck>
     */
    private Collection $configuredChecks;

    /**
     * @ApiProperty(push=true)
     * @ApiSubresource()
     * @ORM\OrderBy({"createdAt": "DESC"})
     * @ORM\OneToMany(targetEntity=Run::class, mappedBy="site")
     *
     * @var Collection<Run>
     */
    private Collection $runs;

    public function __construct(string $name, string $url)
    {
        $this->id               = Uuid::uuid4();
        $this->slug             = Urlizer::urlize($name);
        $this->name             = $name;
        $this->url              = $url;
        $this->configuredChecks = new ArrayCollection;
        $this->runs             = new ArrayCollection;

        $this->initialize();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Collection<ConfiguredCheck>
     */
    public function getConfiguredChecks(): Collection
    {
        return $this->configuredChecks;
    }

    /**
     * @return Collection<Run>
     */
    public function getRuns(): Collection
    {
        return $this->runs;
    }

    /**
     * @Groups({"get_sites", "get_site"})
     */
    public function getLastRun(): ?Run
    {
        if (0 === count($this->getRuns())) {
            return null;
        }

        return $this->getRuns()->first();
    }

    public function getLastResultsGroupedByCheckTypes(): array
    {
        $maxDuration = array_reduce(
            $this->getConfiguredChecks()->toArray(),
            static function (int $memo, ConfiguredCheck $configuredCheck) {
                return max($memo, $configuredCheck->getExecutionDelay());
            },
            0
        );

        $runs = $this->getRuns()->filter(
            static function (Run $run) use ($maxDuration) {
                return $run->getUpdatedAt() > (new \DateTime)->sub(new \DateInterval('PT' . $maxDuration . 'S'));
            }
        );

        /** @var RunCheckResult[] $lastCheckResults */
        $lastCheckResults = [];
        foreach ($runs as $run) {
            /** @var $run Run */
            foreach ($run->getCheckResults() as $checkResult) {
                foreach ($lastCheckResults as $lastCheckResult) {
                    if (
                        $lastCheckResult->getConfiguredCheck()->isEqualTo($checkResult->getConfiguredCheck()) &&
                        !$lastCheckResult->isFromSameCheck($checkResult)
                    ) {
                        break 2;
                    }
                }

                $lastCheckResults[] = $checkResult;
            }
        }

        return $lastCheckResults;
    }

    /**
     * @Groups({"get_site"})
     */
    public function getLastLevelsGroupedByCheckers(): array
    {
        $levels = [];
        foreach ($this->getLastResultsGroupedByCheckTypes() as $checkResult) {
            $check = call_user_func([$checkResult->getConfiguredCheck()->getCheck(), 'getName']);

            $levels[$check] = CheckResult::worstLevel($checkResult->getLevel(), $levels[$check] ?? 'success');
        }

        return $levels;
    }

    /**
     * @Groups({"get_sites", "get_site"})
     */
    public function getCurrentLowerResultLevel(): string
    {
        $level = 'success';

        foreach ($this->getLastResultsGroupedByCheckTypes() as $checkResult) {
            if ('error' === $checkResult->getLevel()) {
                return 'error';
            }

            if ('warning' === $checkResult->getLevel()) {
                $level = 'warning';
            }
        }

        return $level;
    }
}

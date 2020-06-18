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
 *      mercure=true,
 *      attributes={"order"={"createdAt": "ASC"}},
 *      normalizationContext={"groups": {"get_run_check_result", "timestamp"}},
 *      itemOperations={
 *          "get"={"normalization_context"={"groups"={"get_run_check_result"}}}
 *      },
 *      collectionOperations={},
 *      subresourceOperations={
 *          "api_runs_check_results_get_subresource": {"normalization_context": {"groups": {"get_run_check_results_for_run", "timestamp"}}}
 *      }
 * )
 * @ORM\Entity()
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 */
class RunCheckResult implements HasTimestamp
{
    use HasTimestampImpl;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     * @Groups({"get_run", "get_run_check_result"})
     */
    private UuidInterface $id;

    /**
     * @Groups("get_run_check_result")
     * @ORM\ManyToOne(targetEntity=Run::class, inversedBy="checkResults")
     */
    private Run $run;

    /**
     * @Groups({"get_site"})
     * @ORM\ManyToOne(targetEntity=ConfiguredCheck::class, cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ConfiguredCheck $configuredCheck;

    /**
     * @Groups({"get_run_check_results_for_run", "get_run_check_result"})
     * @ORM\Column(type="string")
     * @Groups({"get_run", "get_site"})
     */
    private string $level;

    /**
     * @Groups({"get_run_check_results_for_run", "get_run_check_result"})
     * @ORM\Column(type="string")
     * @Groups({"get_run", "get_site"})
     */
    private string $type;

    /**
     * @Groups({"get_run_check_results_for_run", "get_run_check_result"})
     * @ORM\Column(type="json_document")
     * @Groups({"get_run", "get_site"})
     */
    private array $data;

    public function __construct(Run $run, ConfiguredCheck $configuredCheck, CheckResult $checkResult)
    {
        $this->id              = Uuid::uuid4();
        $this->level           = $checkResult->getLevel()->toString();
        $this->type            = $checkResult->getType();
        $this->data            = $checkResult->getData();
        $this->run             = $run;
        $this->configuredCheck = $configuredCheck;

        $this->initialize();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getRun(): Run
    {
        return $this->run;
    }

    public function getConfiguredCheck(): ConfiguredCheck
    {
        return $this->configuredCheck;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isFromSameCheck(self $runCheckResult): bool
    {
        if (!$this->configuredCheck->isEqualTo($runCheckResult->getConfiguredCheck())) {
            return false;
        }

        if (!$this->run->isEqualTo($runCheckResult->getRun())) {
            return false;
        }

        return true;
    }
}

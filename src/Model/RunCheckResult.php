<?php

declare(strict_types=1);

namespace App\Model;

use App\Checker\CheckResult;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class RunCheckResult
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     * @Groups({"get_run"})
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Run::class, inversedBy="checkResults")
     */
    private Run $run;

    /**
     * @ORM\ManyToOne(targetEntity=ConfiguredCheck::class)
     */
    private ConfiguredCheck $configuredCheck;

    /**
     * @ORM\Column(type="string")
     * @Groups({"get_run"})
     */
    private string $level;

    /**
     * @ORM\Column(type="string")
     * @Groups({"get_run"})
     */
    private string $type;

    /**
     * @ORM\Column(type="json_document")
     * @Groups({"get_run"})
     */
    private array $data;

    public function __construct(Run $run, ConfiguredCheck $configuredCheck, CheckResult $checkResult)
    {
        $this->id              = Uuid::uuid4();
        $this->level           = $checkResult->getLevel();
        $this->type            = $checkResult->getType();
        $this->data            = $checkResult->getData();
        $this->run             = $run;
        $this->configuredCheck = $configuredCheck;
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
}

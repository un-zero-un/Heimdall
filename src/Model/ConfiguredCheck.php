<?php

declare(strict_types=1);

namespace App\Model;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table()
 */
class ConfiguredCheck implements HasTimestamp
{
    use HasTimestampImpl;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="configuredChecks")
     */
    private Site $site;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="check_class")
     */
    private string $check;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $executionDelay = null;

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private ?array $config = null;

    public function __construct(Site $site, string $check)
    {
        $this->id    = Uuid::uuid4();
        $this->site  = $site;
        $this->check = $check;

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

    public function getCheck(): string
    {
        return $this->check;
    }

    public function getExecutionDelay(): ?int
    {
        return $this->executionDelay;
    }

    public function setExecutionDelay(?int $executionDelay): void
    {
        $this->executionDelay = $executionDelay;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function setConfig(?array $config): void
    {
        $this->config = $config;
    }
}

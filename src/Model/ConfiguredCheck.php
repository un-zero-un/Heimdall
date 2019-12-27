<?php

declare(strict_types=1);

namespace App\Model;

use App\Behavior\Equatable;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table()
 */
class ConfiguredCheck implements HasTimestamp, Equatable
{
    use HasTimestampImpl;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="configuredChecks")
     */
    private ?Site $site;

    /**
     * @Groups({"get_site"})
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="check_class")
     */
    private string $check;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private int $executionDelay = 0;

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private ?array $config = null;

    /**
     * @Groups({"get_site"})
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private ?string $lastResult = null;

    public function __construct(?Site $site, string $check)
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

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function getCheck(): string
    {
        return $this->check;
    }

    public function getExecutionDelay(): int
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

    public function setLastResult(?string $lastResult): void
    {
        $this->lastResult = $lastResult;
    }

    public function getLastResult(): ?string
    {
        return $this->lastResult;
    }

    public function isEqualTo(Equatable $equatable): bool
    {
        if (!$equatable instanceof self) {
            return false;
        }

        return $equatable->getId()->equals($this->getId());
    }
}

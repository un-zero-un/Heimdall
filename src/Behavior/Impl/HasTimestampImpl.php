<?php

declare(strict_types=1);

namespace App\Behavior\Impl;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\HasLifecycleCallbacks()
 */
trait HasTimestampImpl
{
    /**
     * @Groups({"timestamp"})
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @Groups({"timestamp"})
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $updatedAt;

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    final public function initialize(): void
    {
        $this->createdAt = new \DateTimeImmutable;
        $this->updatedAt = new \DateTimeImmutable;
    }
    /**
     * @ORM\PreUpdate()
     */
    public function updateTimestampOnUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
    }
}

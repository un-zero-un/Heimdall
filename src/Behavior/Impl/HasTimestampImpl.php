<?php

declare(strict_types=1);

namespace App\Behavior\Impl;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 */
trait HasTimestampImpl
{
    /**
     * @ORM\Column(type="date_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="date_immutable")
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

    public function initialize(): void
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

<?php

declare(strict_types=1);

namespace App\Behavior;

interface HasTimestamp
{
    public function getCreatedAt(): \DateTimeImmutable;

    public function getUpdatedAt(): \DateTimeImmutable;
}

<?php

declare(strict_types=1);

namespace App\Behavior;

interface Equatable
{
    public function isEqualTo(Equatable $equatable): bool;
}

<?php

declare(strict_types=1);

namespace App\Checker;

interface ConfigurableChecker
{
    public static function getConfigFormType(): string;
}

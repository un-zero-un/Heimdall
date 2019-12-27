<?php

declare(strict_types=1);

namespace App\Checker;

use App\ValueObject\ResultLevel;

class CheckResult
{
    private ResultLevel $level;

    private string $type;

    private array $data;

    public function __construct(ResultLevel $level, string $type, array $data = [])
    {
        $this->level = $level;
        $this->type  = $type;
        $this->data  = $data;
    }

    public function getLevel(): ResultLevel
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

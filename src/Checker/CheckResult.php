<?php

declare(strict_types=1);

namespace App\Checker;

class CheckResult
{
    private string $level;

    private string $type;

    private array $data;

    public function __construct(string $level, string $type, array $data = [])
    {
        $this->level = $level;
        $this->type  = $type;
        $this->data  = $data;
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

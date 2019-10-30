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

    public static function worstLevel(string $level1, string $level2): string
    {
        if ('error' === $level1 || 'error' === $level2) {
            return 'error';
        }

        if ('warning' === $level1 || 'warning' === $level2) {
            return 'warning';
        }

        return 'success';
    }
}

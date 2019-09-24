<?php

declare(strict_types=1);

namespace App\Checker;

use Symfony\Component\Serializer\Annotation\Groups;

class CheckResult
{
    /**
     * @Groups({"get_run"})
     */
    private string $level;

    /**
     * @Groups({"get_run"})
     */
    private string $type;

    /**
     * @Groups({"get_run"})
     */
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

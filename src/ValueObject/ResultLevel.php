<?php

declare(strict_types=1);

namespace App\ValueObject;

class ResultLevel
{
    private const LEVEL_WEIGHTS = [
        self::UNKNOWN => 0x00,
        self::SUCCESS => 0x10,
        self::WARNING => 0x20,
        self::ERROR   => 0x30,
    ];

    public const ERROR   = 'error';
    public const WARNING = 'warning';
    public const SUCCESS = 'success';
    public const UNKNOWN = 'unknown';

    private string $level;

    private function __construct(string $level)
    {
        $this->level = $level;
    }

    public function isWorstThan(self $level): bool
    {
        return self::LEVEL_WEIGHTS[$this->level] > self::LEVEL_WEIGHTS[$level->level];
    }

    public function toString(): string
    {
        return $this->level;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(string $level): self
    {
        return new self($level);
    }

    /**
     * @param self[] $levels
     *
     * @return self
     */
    public static function findWorst(array $levels): self
    {
        $maxLevel     = array_key_last(self::LEVEL_WEIGHTS);
        $currentLevel = self::fromString(array_key_first(self::LEVEL_WEIGHTS));
        foreach ($levels as $level) {
            if ($level->level === $maxLevel) {
                return new self(array_key_last(self::LEVEL_WEIGHTS));
            }

            if (self::LEVEL_WEIGHTS[$level->level] > self::LEVEL_WEIGHTS[$currentLevel->level]) {
                $currentLevel = $level;
            }
        }

        return $currentLevel;
    }

    public static function success(): self
    {
        return self::fromString(self::SUCCESS);
    }

    public static function warning(): self
    {
        return self::fromString(self::WARNING);
    }

    public static function error(): self
    {
        return self::fromString(self::ERROR);
    }
}

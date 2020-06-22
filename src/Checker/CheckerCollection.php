<?php

declare(strict_types=1);

namespace App\Checker;

use App\Checker\Exception\UnknownCheckerException;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;

class CheckerCollection
{
    /**
     * @var iterable<Checker>
     */
    private $checkers;

    public function __construct(iterable $checkers)
    {
        $this->checkers = $checkers;
    }

    public function get(string $name): Checker
    {
        foreach ($this->checkers as $checker) {
            if (get_class($checker) === $name) {
                return $checker;
            }
        }

        throw new UnknownCheckerException($name);
    }

    public function findFromAlias(string $name): Checker
    {
        try {
            return $this->get($name);
        } catch (UnknownCheckerException $e) {
        }

        try {
            return $this->get(__NAMESPACE__ . '\\' . $name);
        } catch (UnknownCheckerException $e) {
        }

        try {
            return $this->get(__NAMESPACE__ . '\\' . $name . 'Checker');
        } catch (UnknownCheckerException $e) {
        }

        return $this->get(__NAMESPACE__ . '\\' . (new Inflector(new NoopWordInflector, new NoopWordInflector))->classify($name) . 'Checker');
    }

    /**
     * @return iterable<Checker>
     */
    public function all(): iterable
    {
        return $this->checkers;
    }
}

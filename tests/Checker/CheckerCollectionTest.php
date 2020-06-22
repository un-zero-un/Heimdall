<?php

declare(strict_types=1);

namespace App\Tests\Checker;

use App\Checker\Checker1Checker;
use App\Checker\Checker2Checker;
use App\Checker\Checker3Checker;
use App\Checker\CheckerCollection;
use PHPUnit\Framework\TestCase;

class CheckerCollectionTest extends TestCase
{
    private CheckerCollection $checkerCollection;

    public function setUp()
    {
        $this->checkerCollection = new CheckerCollection(
            (static function () {
                yield new Checker1Checker();
                yield new Checker2Checker();
                yield new Checker3Checker();
            })()
        );
    }

    public function test_it_returns_checker()
    {
        $this->assertInstanceOf(Checker2Checker::class, $this->checkerCollection->get(Checker2Checker::class));
    }

    public function test_it_find_checker_with_various_aliases()
    {
        $this->assertInstanceOf(Checker2Checker::class, $this->checkerCollection->findFromAlias(Checker2Checker::class));
        $this->assertInstanceOf(Checker1Checker::class, $this->checkerCollection->findFromAlias('Checker1Checker'));
        $this->assertInstanceOf(Checker3Checker::class, $this->checkerCollection->findFromAlias('Checker3'));
    }

    public function test_it_returns_all_checkers()
    {
        $this->assertCount(3, $this->checkerCollection->all());
    }
}

namespace App\Checker;

use App\Model\Site;

class CheckerStub implements Checker
{
    public function check(Site $site, array $config = []): iterable
    {
        return [];
    }

    public function getDefaultExecutionDelay(): int
    {
        return 0;
    }

    public static function getName(): string
    {
        return self::class;
    }
}

class Checker1Checker extends CheckerStub {}
class Checker2Checker extends CheckerStub {}
class Checker3Checker extends CheckerStub {}

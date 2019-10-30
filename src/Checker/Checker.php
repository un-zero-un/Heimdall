<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Site;

interface Checker
{
    /**
     * @param Site $site
     * @param array $config
     *
     * @return iterable<CheckResult>
     */
    public function check(Site $site, array $config = []): iterable;

    public function getDefaultExecutionDelay(): int;

    public static function getName(): string;
}

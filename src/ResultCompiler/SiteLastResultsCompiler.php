<?php

declare(strict_types=1);

namespace App\ResultCompiler;

use App\Checker\CheckResult;
use App\Model\ConfiguredCheck;
use App\Model\Run;
use App\Model\RunCheckResult;
use App\Model\Site;

class SiteLastResultsCompiler
{
    public function getLastResultsGroupedByCheckTypes(Site $site): array
    {
        $maxDuration = array_reduce(
            $site->getConfiguredChecks()->toArray(),
            static function (int $memo, ConfiguredCheck $configuredCheck) {
                return max($memo, $configuredCheck->getExecutionDelay());
            },
            0
        );

        $lastRun = $site->getLastRun();
        if (null === $lastRun) {
            return [];
        }
        $runs = $site->getRuns()->filter(
            static function (Run $run) use ($maxDuration, $lastRun) {
                return $run->getUpdatedAt() > $lastRun->getUpdatedAt()->sub(new \DateInterval('PT' . $maxDuration . 'S'));
            }
        );

        /** @var RunCheckResult[] $lastCheckResults */
        $lastCheckResults = [];
        foreach ($runs as $run) {
            /** @var $run Run */
            foreach ($run->getCheckResults() as $checkResult) {
                foreach ($lastCheckResults as $lastCheckResult) {
                    if (
                        $lastCheckResult->getConfiguredCheck()->isEqualTo($checkResult->getConfiguredCheck()) &&
                        !$lastCheckResult->isFromSameCheck($checkResult)
                    ) {
                        break 2;
                    }
                }

                $lastCheckResults[] = $checkResult;
            }
        }

        return $lastCheckResults;
    }

    public function getLastLevelsGroupedByCheckers(Site $site): array
    {
        $levels = [];
        foreach ($this->getLastResultsGroupedByCheckTypes($site) as $checkResult) {
            $check = call_user_func([$checkResult->getConfiguredCheck()->getCheck(), 'getName']);

            $levels[$check] = CheckResult::worstLevel($checkResult->getLevel(), $levels[$check] ?? 'success');
        }

        return $levels;
    }

    public function getCurrentLowerResultLevel(Site $site): string
    {
        if (null === $site->getLastRun()) {
            return 'unknown';
        }

        $level = 'success';
        foreach ($this->getLastResultsGroupedByCheckTypes($site) as $checkResult) {
            if ('error' === $checkResult->getLevel()) {
                return 'error';
            }

            if ('warning' === $checkResult->getLevel()) {
                $level = 'warning';
            }
        }

        return $level;
    }
}

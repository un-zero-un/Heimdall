<?php

declare(strict_types=1);

namespace App\ResultCompiler;

use App\Model\ConfiguredCheck;
use App\Model\Run;
use App\Model\RunCheckResult;
use App\Model\Site;
use App\ValueObject\ResultLevel;

class SiteLastResultsCompiler
{
    public function getCurrentLowerResultLevel(Site $site): string
    {
        if (null === $site->getLastRun()) {
            return ResultLevel::UNKNOWN;
        }

        return ResultLevel::findWorst(
            array_map(
                fn (RunCheckResult $checkResult) => ResultLevel::fromString($checkResult->getLevel()),
                $this->getLastResultsGroupedByCheckTypes($site)
            )
        )->toString();
    }

    private function getLastResultsGroupedByCheckTypes(Site $site): array
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
}

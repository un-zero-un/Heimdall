<?php

declare(strict_types=1);

namespace App\ResultCompiler;

use App\Model\ConfiguredCheck;
use App\Model\Run;
use App\Model\RunCheckResult;
use App\Model\Site;
use App\Repository\RunRepository;
use App\ValueObject\ResultLevel;
use Doctrine\ORM\UnexpectedResultException;

class SiteLastResultsCompiler
{
    private RunRepository $runRepository;

    public function __construct(RunRepository $runRepository)
    {
        $this->runRepository = $runRepository;
    }

    public function getCurrentLowerResultLevel(Site $site): string
    {
        try {
            return ResultLevel::findWorst(
                array_map(
                    fn(RunCheckResult $checkResult) => ResultLevel::fromString($checkResult->getLevel()),
                    $this->getLastResultsGroupedByCheckTypes($site)
                )
            )->toString();
        } catch (UnexpectedResultException $e) {
            return ResultLevel::UNKNOWN;
        }
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

        $lastRun = $this->runRepository->findLastForSite($site);
        if (null === $lastRun) {
            return [];
        }

        $runs = $this->runRepository->findLastsForSite($site, $maxDuration);

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

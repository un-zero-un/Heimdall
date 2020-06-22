<?php

declare(strict_types=1);

namespace App\ResultCompiler;

use App\Model\ConfiguredCheck;
use App\Model\Run;
use App\Model\RunCheckResult;
use App\Model\Site;
use App\Repository\RunCheckResultRepository;
use App\Repository\RunRepository;
use App\ValueObject\ResultLevel;
use Doctrine\ORM\UnexpectedResultException;

class SiteLastResultsCompiler
{
    private RunRepository $runRepository;

    private RunCheckResultRepository $runCheckResultRepository;

    public function __construct(RunRepository $runRepository, RunCheckResultRepository $runCheckResultRepository)
    {
        $this->runRepository            = $runRepository;
        $this->runCheckResultRepository = $runCheckResultRepository;
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
        $runCheckResults = $this->runCheckResultRepository->findLastOfEachCheckClassForSite($site);

        return array_reduce(
            $runCheckResults,
            static function (array $memo, RunCheckResult $result) {
                $check = $result->getConfiguredCheck()->getCheck();
                if (!isset($memo[$check])) {
                    $memo[$check] = $result;

                    return $memo;
                }

                if (ResultLevel::findWorst([$result->getLevel(), $memo[$check]->getLevel()]) !== $memo[$check]->getLevel()) {
                    $memo[$check] = $result;
                }

                return $memo;
            },
            []
        );
    }
}

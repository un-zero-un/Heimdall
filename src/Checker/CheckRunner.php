<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\ConfiguredCheck;
use App\Model\Run;
use App\Model\RunCheckResult;
use App\Model\Site;
use App\Notifier\RunResultsNotifier;
use App\Repository\RunCheckResultRepository;
use App\Repository\RunRepository;
use App\Repository\SiteRepository;
use App\ResultCompiler\SiteLastResultsCompiler;
use App\ValueObject\ResultLevel;
use Doctrine\ORM\UnexpectedResultException;

class CheckRunner
{
    private SiteRepository $siteRepository;

    private CheckerCollection $checkerCollection;

    private RunRepository $runRepository;

    private RunResultsNotifier $notifier;

    private SiteLastResultsCompiler $lastResultsCompiler;

    private RunCheckResultRepository $runCheckResultRepository;

    public function __construct(
        RunRepository            $runRepository,
        RunCheckResultRepository $runCheckResultRepository,
        SiteRepository           $siteRepository,
        CheckerCollection        $checkerCollection,
        RunResultsNotifier       $notifier,
        SiteLastResultsCompiler  $lastResultsCompiler
    )
    {
        $this->runRepository            = $runRepository;
        $this->siteRepository           = $siteRepository;
        $this->checkerCollection        = $checkerCollection;
        $this->notifier                 = $notifier;
        $this->lastResultsCompiler      = $lastResultsCompiler;
        $this->runCheckResultRepository = $runCheckResultRepository;
    }

    /**
     * @return \Generator<RunCheckResult>
     */
    public function runForSite(Site $site): \Generator
    {
        $run = Run::publish($site);

        $run->begin();
        $this->runRepository->save($run);

        /** @var RunCheckResult[] $lastRunCheckResults */
        $lastRunCheckResults = $this->runCheckResultRepository->findLastOfEachCheckClassForSite($site);

        foreach ($site->getConfiguredChecks() as $configuredCheck) {

            /** @var ConfiguredCheck $configuredCheck */
            $checker        = $this->checkerCollection->get($configuredCheck->getCheck());
            $executionDelay = $configuredCheck->getExecutionDelay() ?: $checker->getDefaultExecutionDelay();
            foreach ($lastRunCheckResults as $runCheckResult) {
                if (
                    $runCheckResult->getConfiguredCheck()->isEqualTo($configuredCheck) &&
                    (time() - $runCheckResult->getCreatedAt()->getTimestamp()) < $executionDelay) {
                    continue 2;
                }
            }

            $results = $checker->check($site, $configuredCheck->getConfig() ?: []);
            $results = is_array($results) ? $results : iterator_to_array($results);

            foreach ($results as $result) {
                $runCheckResult = $run->addCheckResult($configuredCheck, $result);
                $this->runRepository->update($run);

                yield $runCheckResult;
            }
        }

        if (0 === $run->getCheckResults()->count()) {
            $this->runRepository->remove($run);

            return;
        }

        $this->runRepository->update($run);

        $run->finish($this->lastResultsCompiler->getCurrentLowerResultLevel($site));
        $this->runRepository->update($run);
    }

    /**
     * @return \Generator<RunCheckResult>
     */
    public function runAll(): \Generator
    {
        $results = [];
        foreach ($this->siteRepository->findAll() as $site) {
            foreach ($this->runForSite($site) as $result) {
                $results[] = $result;

                yield $result;
            }
        }

        $this->notifier->notify(
            array_unique(
                array_map(
                    static fn (RunCheckResult $result) => $result->getRun(),
                    $results
                ),
                SORT_REGULAR
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\ConfiguredCheck;
use App\Model\Run;
use App\Model\RunCheckResult;
use App\Model\Site;
use App\Notifier\RunResultsNotifier;
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

    public function __construct(
        RunRepository $runRepository,
        SiteRepository $siteRepository,
        CheckerCollection $checkerCollection,
        RunResultsNotifier $notifier,
        SiteLastResultsCompiler $lastResultsCompiler
    ) {
        $this->runRepository       = $runRepository;
        $this->siteRepository      = $siteRepository;
        $this->checkerCollection   = $checkerCollection;
        $this->notifier            = $notifier;
        $this->lastResultsCompiler = $lastResultsCompiler;
    }

    /**
     * @return \Generator<RunCheckResult>
     */
    public function runForSite(Site $site): \Generator
    {
        try {
            $lastRun = $this->runRepository->findLastForSite($site);
        } catch (UnexpectedResultException $e) {
            $lastRun = null;
        }
        $run = Run::publish($site);

        $run->begin();
        $this->runRepository->save($run);

        foreach ($site->getConfiguredChecks() as $configuredCheck) {
            /** @var ConfiguredCheck $configuredCheck */
            $checker        = $this->checkerCollection->get($configuredCheck->getCheck());
            $executionDelay = $configuredCheck->getExecutionDelay() ?: $checker->getDefaultExecutionDelay();
            if ($lastRun && (time() - $lastRun->getCreatedAt()->getTimestamp() < $executionDelay)) {
                continue;
            }

            $results = $checker->check($site, $configuredCheck->getConfig() ?: []);
            $results = is_array($results) ? $results : iterator_to_array($results);

            $configuredCheck->setLastResult(
                ResultLevel::findWorst(
                    array_map(
                        fn(CheckResult $checkResult) => $checkResult->getLevel(),
                        $results
                    )
                )->toString()
            );

            foreach ($results as $result) {
                $runCheckResult = $run->addCheckResult($configuredCheck, $result);
                $this->runRepository->update($run);

                yield $runCheckResult;
            }
        }

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
                    fn(RunCheckResult $result) => $result->getRun(),
                    $results
                ),
                SORT_REGULAR
            )
        );
    }
}

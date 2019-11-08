<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Run;
use App\Model\Site;
use App\Notifier\RunResultsNotifier;
use App\Repository\RunRepository;
use App\Repository\SiteRepository;

class CheckRunner
{
    private SiteRepository $siteRepository;

    private CheckerCollection $checkerCollection;

    private RunRepository $runRepository;

    private RunResultsNotifier $notifier;

    public function __construct(
        RunRepository $runRepository,
        SiteRepository $siteRepository,
        CheckerCollection $checkerCollection,
        RunResultsNotifier $notifier
    )
    {
        $this->runRepository     = $runRepository;
        $this->siteRepository    = $siteRepository;
        $this->checkerCollection = $checkerCollection;
        $this->notifier          = $notifier;
    }

    public function runForSite(Site $site): Run
    {
        $lastRun = $site->getLastRun();
        $run     = Run::publish($site);

        $run->begin();
        $this->runRepository->save($run);
        foreach ($site->getConfiguredChecks() as $configuredCheck) {
            $checker        = $this->checkerCollection->get($configuredCheck->getCheck());
            $executionDelay = $configuredCheck->getExecutionDelay() ?: $checker->getDefaultExecutionDelay();
            if ($lastRun && (time() - $lastRun->getCreatedAt()->getTimestamp() < $executionDelay)) {
                continue;
            }

            $results = $checker->check($site, $configuredCheck->getConfig() ?: []);
            foreach ($results as $result) {
                $run->addCheckResult($configuredCheck, $result);
                $this->runRepository->update($run);
            }
        }

        $run->finish();
        $this->runRepository->update($run);

        return $run;
    }

    public function runAll(): void
    {
        $runs = [];
        foreach ($this->siteRepository->findAll() as $site) {
            $runs[] = $this->runForSite($site);
        }

        $this->notifier->notify($runs);
    }
}

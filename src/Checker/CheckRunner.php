<?php

declare(strict_types=1);

namespace App\Checker;

use App\Model\Run;
use App\Model\Site;
use App\Repository\RunRepository;
use App\Repository\SiteRepository;

class CheckRunner
{
    private SiteRepository $siteRepository;

    private CheckerCollection $checkerCollection;

    private RunRepository $runRepository;

    public function __construct(RunRepository $runRepository, SiteRepository $siteRepository, CheckerCollection $checkerCollection)
    {
        $this->runRepository     = $runRepository;
        $this->siteRepository    = $siteRepository;
        $this->checkerCollection = $checkerCollection;
    }

    public function runForSite(Site $site): void
    {
        $lastRun      = $site->getLastRun();
        $run          = Run::publish($site);

        $run->begin();
        $this->runRepository->save($run);
        foreach ($site->getConfiguredChecks() as $configuredCheck) {
            $checker        = $this->checkerCollection->get($configuredCheck->getCheck());
            $executionDelay = $configuredCheck->getExecutionDelay() ?: $checker->getDefaultExecutionDelay();
            if ($lastRun && (time() - $lastRun->getCreatedAt()->getTimestamp() < $executionDelay)) {
                //continue;
            }

            $results = $checker->check($site, $configuredCheck->getConfig() ?: []);
            foreach ($results as $result) {
                $run->addCheckResult($configuredCheck, $result);
            }

            $run->finish();
            $this->runRepository->update($run);
        }
    }

    public function runAll(): void
    {
        foreach ($this->siteRepository->findAll() as $site) {
            $this->runForSite($site);
        }
    }
}

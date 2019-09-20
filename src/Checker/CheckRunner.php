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
        $checkResults = [];
        foreach ($site->getConfiguredChecks() as $configuredCheck) {
            $results = $this->checkerCollection->get(
                $configuredCheck->getCheck())->check($site, $configuredCheck->getConfig() ?: []
            );

            foreach ($results as $result) {
                $checkResults[] = $result;
            }
        }

        $this->runRepository->save(Run::publish($site, $checkResults));
    }

    public function runAll(): void
    {
        foreach ($this->siteRepository->findAll() as $site) {
            $this->runForSite($site);
        }
    }
}

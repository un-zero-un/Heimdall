<?php

namespace App\Command;

use App\Checker\CheckerCollection;
use App\Repository\SiteRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunSiteChecksCommand extends Command
{
    private CheckerCollection $checkerCollection;

    private SiteRepository $siteRepository;

    private CheckResultCliFormatter $formatter;

    public function __construct(CheckerCollection $checkerCollection, SiteRepository $siteRepository, CheckResultCliFormatter $formatter)
    {
        parent::__construct();

        $this->checkerCollection = $checkerCollection;
        $this->siteRepository    = $siteRepository;
        $this->formatter         = $formatter;
    }

    protected function configure(): void
    {
        $this
            ->setName('heimdall:run-site-checks')
            ->setDescription('Run all checks on a single site')
            ->addArgument('site', InputArgument::REQUIRED, 'The slug of the site to be checked');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @psalm-suppress PossiblyInvalidArgument */
        $site = $this->siteRepository->findOneBySlug($input->getArgument('site'));

        $io->note(sprintf('Running checks on site "%s"', $site->getName()));

        foreach ($site->getConfiguredChecks() as $configuredCheck) {
            $checker = $this->checkerCollection->get($configuredCheck->getCheck());
            $results = $checker->check($site, $configuredCheck->getConfig() ?: []);

            foreach ($results as $result) {
                $this->formatter->formatCheckResult($io, $result);
            }
        }

        return 0;
    }
}

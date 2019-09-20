<?php

namespace App\Command;

use App\Checker\CheckerCollection;
use App\Checker\CheckResult;
use App\Repository\SiteRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCheckCommand extends Command
{
    private CheckerCollection $checkerCollection;

    private SiteRepository $siteRepository;

    private CheckResultCliFormatter $formatter;

    public function __construct(CheckerCollection $checkerCollection, SiteRepository $siteRepository, CheckResultCliFormatter $formatter)
    {
        parent::__construct();

        $this->checkerCollection = $checkerCollection;
        $this->siteRepository    = $siteRepository;
        $this->formatter = $formatter;
    }

    protected function configure(): void
    {
        $this
            ->setName('heimdall:run-check')
            ->setDescription('Run a single check on a single site')
            ->addArgument('site', InputArgument::REQUIRED, 'The slug of the site to be checked')
            ->addArgument('check', InputArgument::REQUIRED, 'The check to run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        /** @psalm-suppress PossiblyInvalidArgument */
        $site    = $this->siteRepository->findOneBySlug($input->getArgument('site'));
        /** @psalm-suppress PossiblyInvalidArgument */
        $checker = $this->checkerCollection->findFromAlias($input->getArgument('check'));

        $io->note(sprintf('Running check "%s" on site "%s"', get_class($checker), $site->getName()));

        $results = $checker->check($site);
        foreach ($results as $result) {
            $this->formatter->formatCheckResult($io, $result);
        }
    }
}

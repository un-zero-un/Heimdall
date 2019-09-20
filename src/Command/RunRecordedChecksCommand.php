<?php

namespace App\Command;

use App\Checker\CheckRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunRecordedChecksCommand extends Command
{
    private CheckRunner $checkRunner;

    public function __construct(CheckRunner $checkRunner)
    {
        parent::__construct();

        $this->checkRunner = $checkRunner;
    }

    protected function configure(): void
    {
        $this
            ->setName('heimdall:run-recorded-checks')
            ->setDescription('Run all checks on all sites, recorded but silent');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Running all checks');

        $this->checkRunner->runAll();
    }
}

<?php

namespace App\Command;

use App\Checker\CheckRunner;
use App\Model\RunCheckResult;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;

class RunRecordedChecksCommand extends Command
{
    const NAME = 'heimdall:run-recorded-checks';

    private CheckRunner $checkRunner;

    private LockFactory $lockFactory;

    public function __construct(CheckRunner $checkRunner, LockFactory $lockFactory)
    {
        parent::__construct();

        $this->checkRunner = $checkRunner;
        $this->lockFactory = $lockFactory;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Run all checks on all sites, recorded but silent');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        /*$lock = $this->lockFactory->createLock(self::NAME, 600);

        if (!$lock->acquire()) {
            $io->error('A check is already running.');

            return 0;
        }*/

        $io->note('Running all checks');

        foreach ($this->checkRunner->runAll() as $runCheckResult) {
            /** @var RunCheckResult $runCheckResult */
            $io->comment(
                sprintf(
                    '%s, %s : %s',
                    $runCheckResult->getRun()->getSite(),
                    $runCheckResult->getConfiguredCheck()->getCheck(),
                    $runCheckResult->getLevel()
                )
            );
        }

        //$lock->release();

        return 0;
    }
}

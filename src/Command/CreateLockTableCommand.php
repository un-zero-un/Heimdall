<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\Store\PdoStore;
use Symfony\Component\Lock\StoreInterface;

class CreateLockTableCommand extends Command
{
    private StoreInterface $store;

    public function __construct(StoreInterface $store)
    {
        $this->store = $store;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('heimdall:lock:create-table');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->store instanceof PdoStore) {
            $io->warning('Not a PDO lock store. Skipping.');

            return;
        }

        $this->store->createTable();

        $io->success('Done');
    }
}

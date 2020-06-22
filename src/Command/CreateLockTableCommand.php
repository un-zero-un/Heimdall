<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\PdoStore;

class CreateLockTableCommand extends Command
{
    private PersistingStoreInterface $store;

    private Connection $connection;

    public function __construct(PersistingStoreInterface $store, Connection $connection)
    {
        $this->store = $store;
        $this->connection = $connection;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('heimdall:lock:create-table')
            ->addOption('drop', 'd', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('drop')) {
            $this->connection->exec('DROP TABLE IF EXISTS lock_keys;');
        }


        if (!$this->store instanceof PdoStore) {
            $io->warning('Not a PDO lock store. Skipping.');

            return 0;
        }

        $this->store->createTable();

        $io->success('Done');

        return 0;
    }
}

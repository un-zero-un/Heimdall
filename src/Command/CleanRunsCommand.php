<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanRunsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('heimdall:clean-runs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager
            ->createNativeQuery(
                <<<SQL
                DELETE FROM run_check_result rcr
                WHERE rcr.level != 'success'
                AND   rcr.created_at < NOW() - INTERVAL '1 month'
                AND   EXTRACT(MINUTES FROM rcr.created_at) > 4
            SQL,
                new ResultSetMapping,
            )
            ->execute();

        $this->entityManager
            ->createNativeQuery(
                <<<SQL
                DELETE FROM run WHERE id IN (
                    SELECT run.id
                    FROM run
                             LEFT JOIN run_check_result rcr on run.id = rcr.run_id
                    WHERE rcr.id IS NULL
                );
            SQL,
                new ResultSetMapping,
            )
            ->execute();

        return self::SUCCESS;
    }
}

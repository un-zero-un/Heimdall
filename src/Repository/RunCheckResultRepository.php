<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\RunCheckResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class RunCheckResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RunCheckResult::class);
    }

    public function save(RunCheckResult $runCheckResult): void
    {
        $this->getEntityManager()->persist($runCheckResult);
        $this->getEntityManager()->flush();
    }

    public function update(RunCheckResult $runCheckResult): void
    {
        $this->getEntityManager()->flush();
    }
}

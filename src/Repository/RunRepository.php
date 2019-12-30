<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Run;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Run::class);
    }

    public function save(Run $run): void
    {
        $this->getEntityManager()->persist($run);
        $this->getEntityManager()->flush();
    }

    public function update(Run $run): void
    {
        $this->getEntityManager()->flush();
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Run;
use App\Model\Site;
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

    public function remove(Run $run): void
    {
        $this->getEntityManager()->remove($run);
        $this->getEntityManager()->flush();
    }

    public function findLastForSite(Site $site): Run
    {
        return $this->createQueryBuilder('run')
            ->where('run.site = :site')
            ->orderBy('run.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('site', $site)
            ->getQuery()
            ->getSingleResult();
    }

    public function findLastsForSite(Site $site, int $maxDelay): array
    {
        return $this->createQueryBuilder('run')
            ->where('run.site = :site')
            ->andWhere('run.updatedAt >= :date_limit')
            ->orderBy('run.createdAt', 'DESC')
            ->setParameter('site', $site)
            ->setParameter('date_limit', new \DateTimeImmutable('-' . $maxDelay . ' seconds'))
            ->getQuery()
            ->getResult();

    }

    public function findLasts(Site $site): array
    {
        return $this->createQueryBuilder('run')
            ->where('run.site = :site')
            ->orderBy('run.createdAt', 'DESC')
            ->setParameter('site', $site)
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }

    public function findPrevious(Run $run): ?Run
    {
        return $this->createQueryBuilder('run')
            ->where('run.createdAt < :date')
            ->andWhere('run.site = :site')
            ->orderBy('run.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('date', $run->getCreatedAt())
            ->setParameter('site', $run->getSite())
            ->getQuery()
            ->getOneOrNullResult();
    }
}

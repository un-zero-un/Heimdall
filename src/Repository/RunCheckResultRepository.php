<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\ConfiguredCheck;
use App\Model\RunCheckResult;
use App\Model\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

class RunCheckResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RunCheckResult::class);
    }

    public function findLastOfEachCheckClassForSite(Site $site): array
    {
        $tableName                = $this->getClassMetadata()->getTableName();
        $configuredCheckTableName = $this->getEntityManager()->getClassMetadata(ConfiguredCheck::class)->getTableName();

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(RunCheckResult::class, 'run_check_result');

        return $this->getEntityManager()
            ->createNativeQuery(
                <<<SQL
                    SELECT run_check_result.*
                    FROM ${tableName} run_check_result
                    INNER JOIN ${configuredCheckTableName} configured_check ON configured_check.id = run_check_result.configured_check_id
                    WHERE 
                          configured_check.site_id = :site_id
                      AND (configured_check.site_id, configured_check.check_class, configured_check.config, run_check_result.created_at)
                       IN (
                              SELECT configured_check_inner.site_id, configured_check_inner.check_class, configured_check_inner.config, MAX(run_check_result_inner.created_at)
                              FROM ${tableName} run_check_result_inner
                              INNER JOIN ${configuredCheckTableName} configured_check_inner ON run_check_result_inner.configured_check_id = configured_check_inner.id
                              GROUP BY configured_check_inner.site_id, configured_check_inner.check_class, configured_check_inner.config
                    )
                SQL,
                $rsm
            )
            ->setParameter('site_id', $site->getId())
            ->getResult();
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

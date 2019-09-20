<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

    public function findOneBySlug(string $slug): Site
    {
        return $this->createQueryBuilder('site')
            ->where('site.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @return array<Site>
     */
    public function findAll(): array
    {
        return parent::findAll();
    }
}

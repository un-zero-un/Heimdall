<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\BrowserNotificationSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BrowserNotificationSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BrowserNotificationSubscription::class);
    }

    /**
     * @return BrowserNotificationSubscription[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }
}

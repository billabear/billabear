<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats;

use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\CustomerSubscriptionEvent;
use BillaBear\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Parthenon\Billing\Enum\SubscriptionStatus;

class NewSubscriptionStatsRepository implements NewSubscriptionStatsRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getExistingSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        $startOfMonth = clone $month;
        $startOfMonth->setTime(0, 0, 0);
        $startOfMonth->modify('first day of this month');

        $endOfMonth = clone $startOfMonth;
        $endOfMonth->modify('last day of this month');
        $endOfMonth->setTime(23, 59, 59);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(s.id)')
            ->from(Subscription::class, 's')
            ->where('s.createdAt < :startOfMonth')
            ->andWhere('s.status = :status')
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('status', SubscriptionStatus::ACTIVE);

        if ($brandSettings) {
            $qb->innerJoin('s.customer', 'c')
                ->andWhere('c.brandSettings = :brandSettings')
                ->setParameter('brandSettings', $brandSettings);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getNewSubscriptionsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->getEventCountForMonth(
            $month,
            CustomerSubscriptionEventType::ACTIVATED,
            $brandSettings
        );
    }

    public function getUpgradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->getEventCountForMonth(
            $month,
            CustomerSubscriptionEventType::UPGRADED,
            $brandSettings
        );
    }

    public function getDowngradesCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->getEventCountForMonth(
            $month,
            CustomerSubscriptionEventType::DOWNGRADED,
            $brandSettings
        );
    }

    public function getCancellationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->getEventCountForMonth(
            $month,
            CustomerSubscriptionEventType::CHURNED,
            $brandSettings
        );
    }

    public function getReactivationsCountForMonth(\DateTime $month, ?BrandSettings $brandSettings = null): int
    {
        return $this->getEventCountForMonth(
            $month,
            CustomerSubscriptionEventType::REACTIVATED,
            $brandSettings
        );
    }

    private function getEventCountForMonth(
        \DateTime $month,
        CustomerSubscriptionEventType $eventType,
        ?BrandSettings $brandSettings = null,
    ): int {
        $startOfMonth = clone $month;
        $startOfMonth->setTime(0, 0, 0);
        $startOfMonth->modify('first day of this month');

        $endOfMonth = clone $startOfMonth;
        $endOfMonth->modify('last day of this month');
        $endOfMonth->setTime(23, 59, 59);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(e.id)')
            ->from(CustomerSubscriptionEvent::class, 'e')
            ->where('e.createdAt BETWEEN :startOfMonth AND :endOfMonth')
            ->andWhere('e.eventType = :eventType')
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('endOfMonth', $endOfMonth)
            ->setParameter('eventType', $eventType);

        if ($brandSettings) {
            $qb->innerJoin('e.customer', 'c')
                ->andWhere('c.brandSettings = :brandSettings')
                ->setParameter('brandSettings', $brandSettings);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

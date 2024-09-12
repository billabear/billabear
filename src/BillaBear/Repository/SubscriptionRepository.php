<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Price;
use BillaBear\Entity\SubscriptionPlan;
use Parthenon\Athena\ResultSet;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Common\Exception\NoEntityFoundException;

class SubscriptionRepository extends \Parthenon\Billing\Repository\Orm\SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function getSubscriptionsExpiringInNextFiveMinutes(): array
    {
        // Incase it takes a while to start the process.
        $thirtySecondsAgo = new \DateTime('-30 seconds');
        $fiveMinutes = new \DateTime('+5 minutes');

        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->where('s.validUntil <= :sevenDays')
            ->andWhere('s.status in (:status)')
            ->setParameter('sevenDays', $fiveMinutes)
            ->setParameter('status', [SubscriptionStatus::ACTIVE, SubscriptionStatus::TRIAL_ACTIVE])
            ->orderBy('s.customer');

        return $qb->getQuery()->getResult();
    }

    public function getSubscriptionsExpiringInTwoDays(): array
    {
        $thirtySecondsAgo = new \DateTime('+24 hours');
        $fiveMinutes = new \DateTime('+48 hours');

        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->where('s.validUntil <= :sevenDays')
            ->andWhere('s.status in (:status)')
            ->setParameter('sevenDays', $fiveMinutes)
            ->setParameter('status', [SubscriptionStatus::ACTIVE, SubscriptionStatus::TRIAL_ACTIVE])
            ->orderBy('s.customer');

        return $qb->getQuery()->getResult();
    }

    public function getInvoiceSubscriptionsExpiringInNextFiveMinutes(): array
    {
        // Incase it takes a while to start the process.
        $thirtySecondsAgo = new \DateTime('-30 seconds');
        $fiveMinutes = new \DateTime('+5 minutes');

        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->join('s.customer', 'c')
            ->where('s.validUntil >= :thirtySecondsAgo')
            ->andWhere('s.validUntil <= :sevenDays')
            ->andWhere('s.status in (:status)')
            ->andWhere('c.billingType = :invoiceType')
            ->setParameter('thirtySecondsAgo', $thirtySecondsAgo)
            ->setParameter('sevenDays', $fiveMinutes)
            ->setParameter('status', [SubscriptionStatus::ACTIVE, SubscriptionStatus::TRIAL_ACTIVE])
            ->setParameter('invoiceType', Customer::BILLING_TYPE_INVOICE)
            ->orderBy('s.customer');

        return $qb->getQuery()->getResult();
    }

    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getAllActive(): array
    {
        return $this->entityRepository->findBy(['status' => SubscriptionStatus::ACTIVE]);
    }

    public function getCountActive(): int
    {
        return $this->entityRepository->count(['status' => SubscriptionStatus::ACTIVE]);
    }

    public function getCountOfActiveCustomers(): int
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->select($qb->expr()->countDistinct('s.customer'))
            ->where('s.status = :activeStatus')
            ->setParameter('activeStatus', SubscriptionStatus::ACTIVE);

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    public function getActiveCountForPeriod(\DateTime $startDate, \DateTime $endDate, BrandSettings $brandSettings): int
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('s');
        $queryBuilder->select('COUNT(s)')
            ->innerJoin('s.customer', 'c')
            ->where($queryBuilder->expr()->lte('s.createdAt', ':startDate'))
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull('s.endedAt'),
                    $queryBuilder->expr()->gt('s.endedAt', ':endDate')
                )
            )
            ->andWhere('c.brandSettings = :brandSetting')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('brandSetting', $brandSettings);
        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        return intval($count);
    }

    public function getCreatedCountForPeriod(\DateTime $startDate, \DateTime $endDate, BrandSettings $brandSettings): int
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('s');
        $queryBuilder->select('COUNT(s)')
            ->innerJoin('s.customer', 'c')
            ->where($queryBuilder->expr()->gte('s.createdAt', ':startDate'))
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull('s.endedAt'),
                    $queryBuilder->expr()->gt('s.endedAt', ':endDate')
                )
            )
            ->andWhere('c.brandSettings = :brandSetting')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('brandSetting', $brandSettings);
        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        return intval($count);
    }

    public function getOldestSubscription(): Subscription
    {
        $subscription = $this->entityRepository->findOneBy([], ['createdAt' => 'ASC']);

        if (!$subscription instanceof Subscription) {
            throw new NoEntityFoundException("Can't find any subscription");
        }

        return $subscription;
    }

    public function getPlanCounts(): array
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->select('COUNT(s) as count, p.name')
            ->innerJoin('s.subscriptionPlan', 'p')
            ->groupBy('p.name')
            ->where('s.status = :status')
            ->setParameter('status', SubscriptionStatus::ACTIVE);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getScheduleCounts(): array
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->select('COUNT(s) as count, s.paymentSchedule as name')
            ->groupBy('s.paymentSchedule')
            ->where('s.status = :status')
            ->setParameter('status', SubscriptionStatus::ACTIVE);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findMassChangable(?SubscriptionPlan $subscriptionPlan, ?Price $price, ?BrandSettings $brandSettings, ?string $country): array
    {
        $qb = $this->getMassChangableQueryBuilder($subscriptionPlan, $price, $brandSettings, $country);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function countMassChangable(?SubscriptionPlan $subscriptionPlan, ?Price $price, ?BrandSettings $brandSettings, ?string $country): int
    {
        $qb = $this->getMassChangableQueryBuilder($subscriptionPlan, $price, $brandSettings, $country);
        $qb->select('COUNT(s.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getMassChangableQueryBuilder(?SubscriptionPlan $subscriptionPlan, ?Price $price, ?BrandSettings $brandSettings, ?string $country): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->innerJoin('s.customer', 'c')
            ->where('s.status = :status')
            ->setParameter(':status', SubscriptionStatus::ACTIVE);

        if ($subscriptionPlan) {
            $qb->andWhere('s.subscriptionPlan = :subscription_plan')
                ->setParameter(':subscription_plan', $subscriptionPlan);
        }

        if ($price) {
            $qb->andWhere('s.price = :price')
                ->setParameter(':price', $price);
        }

        if ($brandSettings) {
            $qb->andWhere('c.brandSettings = :brand_settings')
                ->setParameter('brand_settings', $brandSettings);
        }

        if ($country) {
            $qb->andWhere('c.billingAddress.country = :country')
                ->setParameter('country', $country);
        }

        return $qb;
    }

    public function findActiveSubscriptionsOnDate(\DateTime $dateTime, int $count): array
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->where('s.createdAt < :dateTime')
            ->andWhere('s.active = true')
            ->setParameter('dateTime', $dateTime)
            ->setMaxResults($count);

        return $qb->getQuery()->execute();
    }

    public function getAllActiveCountForCustomer(CustomerInterface $customer): int
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->select('COUNT(s) as count')
            ->where('s.status = :status')
            ->andWhere('s.customer = :customer')
            ->setParameter('status', SubscriptionStatus::ACTIVE)
            ->setParameter('customer', $customer);
        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function getAllCancelledCountForCustomer(CustomerInterface $customer): int
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->select('COUNT(s) as count')
            ->where('s.status = :status')
            ->andWhere('s.customer = :customer')
            ->setParameter('status', SubscriptionStatus::CANCELLED)
            ->setParameter('customer', $customer);
        $query = $qb->getQuery();

        $oneCount = (int) $query->getSingleScalarResult();

        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->select('COUNT(s) as count')
            ->where('s.status = :status')
            ->andWhere('s.customer = :customer')
            ->setParameter('status', SubscriptionStatus::PENDING_CANCEL)
            ->setParameter('customer', $customer);
        $query = $qb->getQuery();

        $oneCount += $query->getSingleScalarResult();

        return $oneCount;
    }

    public function getTrialEndingInNextSevenDays(): array
    {
        // Incase it takes a while to start the process.
        $thirtySecondsAgo = new \DateTime('-30 seconds');
        $sevenDays = new \DateTime('+7 days');

        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->join('s.customer', 'c')
            ->where('s.validUntil >= :thirtySecondsAgo')
            ->andWhere('s.validUntil <= :sevenDays')
            ->andWhere('s.status in (:status)')
            ->setParameter('thirtySecondsAgo', $thirtySecondsAgo)
            ->setParameter('sevenDays', $sevenDays)
            ->setParameter('status', [SubscriptionStatus::TRIAL_ACTIVE])
            ->orderBy('s.customer');

        return $qb->getQuery()->getResult();
    }

    public function getLastTenForCustomer($customer): ResultSet
    {
        $results = $this->entityRepository->findBy(['customer' => $customer], ['createdAt' => 'DESC'], 11);

        return new ResultSet($results, 'createdAt', 'DESC', 10);
    }

    public function getSubscriptionWithUsage(): array
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->join('s.price', 'p')
            ->where('p.usage = true')
            ->andWhere('s.status in (:status)')
            ->setParameter('status', [SubscriptionStatus::ACTIVE, SubscriptionStatus::TRIAL_ACTIVE, SubscriptionStatus::PENDING_CANCEL, SubscriptionStatus::OVERDUE_PAYMENT_OPEN]);

        return $qb->getQuery()->getResult();
    }
}

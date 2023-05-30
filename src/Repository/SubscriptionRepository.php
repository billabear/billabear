<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use App\Entity\Customer;
use Parthenon\Billing\Enum\SubscriptionStatus;

class SubscriptionRepository extends \Parthenon\Billing\Repository\Orm\SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function getSubscriptionsExpiringInNextFiveMinutes(): array
    {
        // Incase it takes a while to start the process.
        $thirtySecondsAgo = new \DateTime('-30 seconds');
        $fiveMinutes = new \DateTime('+5 minutes');

        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->where('s.validUntil >= :thirtySecondsAgo')
            ->andWhere('s.validUntil <= :fiveMinutes')
            ->andWhere('s.status = :status')
            ->setParameter('thirtySecondsAgo', $thirtySecondsAgo)
            ->setParameter('fiveMinutes', $fiveMinutes)
            ->setParameter('status', SubscriptionStatus::ACTIVE)
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
            ->andWhere('s.validUntil <= :fiveMinutes')
            ->andWhere('s.status = :status')
            ->andWhere('c.billingType = :invoiceType')
            ->setParameter('thirtySecondsAgo', $thirtySecondsAgo)
            ->setParameter('fiveMinutes', $fiveMinutes)
            ->setParameter('status', SubscriptionStatus::ACTIVE)
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
}

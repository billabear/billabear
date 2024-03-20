<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\PaymentFailureProcess;
use Doctrine\ORM\NoResultException;
use Parthenon\Athena\Repository\DoctrineCrudRepository;

class PaymentFailureProcessRepository extends DoctrineCrudRepository implements PaymentFailureProcessRepositoryInterface
{
    public function findActiveForCustomer(\App\Entity\Customer $customer): ?PaymentFailureProcess
    {
        $qb = $this->entityRepository->createQueryBuilder('pfp');
        $qb->where('pfp.customer = :customer')
            ->andWhere($qb->expr()->notIn('pfp.state', ['payment_complete', 'payment_failure_no_more_retries']))
            ->setParameter('customer', $customer);

        try {
            $result = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            return null;
        }

        return $result;
    }

    public function findRetriesForNextMinute(): array
    {
        $tenSecondsAgo = new \DateTime('-10 seconds');
        $aMinute = new \DateTime('+1 minute');
        $qb = $this->entityRepository->createQueryBuilder('pfp');
        $qb->where('pfp.nextAttemptAt > :tenSecondsAgo')
            ->andWhere('pfp.nextAttemptAt < :aMinute')
            ->andWhere('pfp.state = :retryPayments')
            ->setParameter('tenSecondsAgo', $tenSecondsAgo)
            ->setParameter('aMinute', $aMinute)
            ->setParameter('retryPayments', 'payment_retries');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}

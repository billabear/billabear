<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

class PaymentRepository extends \Parthenon\Billing\Repository\Orm\PaymentRepository implements PaymentRepositoryInterface
{
    public function getPaymentsAmountForCountrySinceDate(string $countryCode, \DateTime $when): array
    {
        $qb = $this->entityRepository->createQueryBuilder('p');
        $qb->select('SUM(p.amount) as amount, p.currency')
            ->where('p.createdAt > :createdAt')
            ->andWhere('p.country = :countryCode')
            ->groupBy('p.currency')
            ->setParameter('countryCode', $countryCode)
            ->setParameter('createdAt', $when);
        $query = $qb->getQuery();

        return $query->getResult();
    }
}

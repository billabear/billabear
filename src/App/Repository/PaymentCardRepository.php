<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use Parthenon\Billing\Repository\Orm\PaymentCardRepository as BaseRepository;

class PaymentCardRepository extends BaseRepository implements PaymentCardRepositoryInterface
{
    public function getExpiringDefaultThisMonth(): array
    {
        $now = new \DateTime();

        $qb = $this->entityRepository->createQueryBuilder('pc');
        $qb->innerJoin('pc.customer', 'c')
            ->innerJoin('c.subscriptions', 's', 'WITH', 's.active = true')
            ->where('pc.expiryYear = :year')
            ->andWhere('pc.expiryMonth = :month')
            ->andWhere('pc.defaultPaymentOption = true')
            ->setParameter(':year', (int) $now->format('Y'))
            ->setParameter(':month', (int) $now->format('m'));

        $cards = $qb->getQuery()->getResult();

        return $cards;
    }
}

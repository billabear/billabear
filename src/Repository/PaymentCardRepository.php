<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

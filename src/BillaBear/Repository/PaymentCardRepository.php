<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Repository\Orm\PaymentCardRepository as BaseRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

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

        return $qb->getQuery()->getResult();
    }

    public function getByStoredPaymentReference(string $reference): PaymentCard
    {
        $result = $this->entityRepository->findOneBy(['storedPaymentReference' => $reference]);

        if (!$result instanceof PaymentCard) {
            throw new NoEntityFoundException('No PaymentCard found with stored reference: '.$reference);
        }

        return $result;
    }
}

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

use Parthenon\Billing\Repository\Orm\PaymentCardRepository as BaseRepository;

class PaymentCardRepository extends BaseRepository implements PaymentCardRepositoryInterface
{
    public function getExpiringDefaultThisMonth(): array
    {
        $now = new \DateTime();
        $cards = $this->entityRepository->findBy(['expiryYear' => (int) $now->format('Y'), 'expiryMonth' => (int) $now->format('m'), 'defaultPaymentOption' => true]);

        return $cards;
    }
}

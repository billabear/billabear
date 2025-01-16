<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface as BaseInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

interface PaymentCardRepositoryInterface extends BaseInterface
{
    /**
     * @return PaymentCard[]
     */
    public function getExpiringDefaultThisMonth(): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getByStoredPaymentReference(string $reference): PaymentCard;
}

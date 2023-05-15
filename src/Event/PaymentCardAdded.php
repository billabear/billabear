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

namespace App\Event;

use App\Entity\Customer;
use Parthenon\Billing\Entity\PaymentCard;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentCardAdded extends Event
{
    public const NAME = 'billabear.payment_card.added';

    public function __construct(
        private Customer $customer,
        private PaymentCard $paymentCard, )
    {
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getPaymentCard(): PaymentCard
    {
        return $this->paymentCard;
    }
}

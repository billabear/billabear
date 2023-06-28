<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\Reports;

use App\Dto\Generic\App\Customer;
use App\Dto\Generic\App\PaymentMethod;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ExpiringCard
{
    #[SerializedName('customer')]
    private Customer $customer;

    #[SerializedName('payment_card')]
    private PaymentMethod $paymentCard;

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getPaymentCard(): PaymentMethod
    {
        return $this->paymentCard;
    }

    public function setPaymentCard(PaymentMethod $paymentCard): void
    {
        $this->paymentCard = $paymentCard;
    }
}

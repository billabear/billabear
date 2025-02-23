<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Reports;

use BillaBear\Dto\Generic\App\Customer;
use BillaBear\Dto\Generic\App\PaymentMethod;
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

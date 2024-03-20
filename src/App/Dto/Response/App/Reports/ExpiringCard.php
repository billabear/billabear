<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

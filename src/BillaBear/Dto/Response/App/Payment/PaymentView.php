<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Payment;

use BillaBear\Dto\Generic\App\Customer;
use BillaBear\Dto\Generic\App\Payment;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PaymentView
{
    private Payment $payment;

    private Customer $customer;

    private array $refunds;

    private array $receipts;

    private array $subscriptions;

    #[SerializedName('max_refundable')]
    private int $maxRefundable;

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getRefunds(): array
    {
        return $this->refunds;
    }

    public function setRefunds(array $refunds): void
    {
        $this->refunds = $refunds;
    }

    public function getMaxRefundable(): int
    {
        return $this->maxRefundable;
    }

    public function setMaxRefundable(int $maxRefundable): void
    {
        $this->maxRefundable = $maxRefundable;
    }

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(array $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function getReceipts(): array
    {
        return $this->receipts;
    }

    public function setReceipts(array $receipts): void
    {
        $this->receipts = $receipts;
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App;

use App\Dto\Generic\App\Customer;
use App\Dto\Response\App\Customer\Limits;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CustomerView
{
    protected Customer $customer;

    #[SerializedName('payment_details')]
    protected array $paymentDetails = [];

    #[SerializedName('subscriptions')]
    protected array $subscriptions = [];

    protected array $payments = [];

    protected array $refunds = [];

    protected bool $success = true;

    protected Limits $limits;

    #[SerializedName('credit')]
    protected array $credit = [];

    protected array $invoices = [];

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getPaymentDetails(): array
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(array $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(array $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function getPayments(): array
    {
        return $this->payments;
    }

    public function setPayments(array $payments): void
    {
        $this->payments = $payments;
    }

    public function getRefunds(): array
    {
        return $this->refunds;
    }

    public function setRefunds(array $refunds): void
    {
        $this->refunds = $refunds;
    }

    public function getLimits(): Limits
    {
        return $this->limits;
    }

    public function setLimits(Limits $limits): void
    {
        $this->limits = $limits;
    }

    public function getCredit(): array
    {
        return $this->credit;
    }

    public function setCredit(array $credit): void
    {
        $this->credit = $credit;
    }

    public function getInvoices(): array
    {
        return $this->invoices;
    }

    public function setInvoices(array $invoices): void
    {
        $this->invoices = $invoices;
    }
}

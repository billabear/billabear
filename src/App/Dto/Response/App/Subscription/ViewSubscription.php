<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Subscription;

use App\Dto\Generic\App\Customer;
use App\Dto\Generic\App\PaymentMethod;
use App\Dto\Generic\App\Product;
use App\Dto\Generic\App\Subscription;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewSubscription
{
    private Subscription $subscription;

    private Customer $customer;

    private Product $product;

    #[SerializedName('payment_details')]
    private PaymentMethod $paymentDetails;

    private array $payments;

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getPaymentDetails(): PaymentMethod
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(PaymentMethod $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    public function getPayments(): array
    {
        return $this->payments;
    }

    public function setPayments(array $payments): void
    {
        $this->payments = $payments;
    }
}

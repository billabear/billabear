<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Subscription;

use BillaBear\Dto\Generic\App\Customer;
use BillaBear\Dto\Generic\App\PaymentMethod;
use BillaBear\Dto\Generic\App\Product;
use BillaBear\Dto\Generic\App\Subscription;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewSubscription
{
    private Subscription $subscription;

    private Customer $customer;

    private ?Product $product;

    #[SerializedName('usage_estimate')]
    private ?UsageEstimate $usageEstimate;

    #[SerializedName('payment_details')]
    private PaymentMethod $paymentDetails;

    #[SerializedName('subscription_events')]
    private array $subscriptionEvents = [];

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
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

    public function getSubscriptionEvents(): array
    {
        return $this->subscriptionEvents;
    }

    public function setSubscriptionEvents(array $subscriptionEvents): void
    {
        $this->subscriptionEvents = $subscriptionEvents;
    }

    public function getUsageEstimate(): ?UsageEstimate
    {
        return $this->usageEstimate;
    }

    public function setUsageEstimate(?UsageEstimate $usageEstimate): void
    {
        $this->usageEstimate = $usageEstimate;
    }
}

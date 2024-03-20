<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Subscription;

use App\Dto\Generic\App\Customer;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CreateView
{
    #[SerializedName('subscription_plans')]
    private array $subscriptionPlans;

    #[SerializedName('payment_details')]
    private array $paymentDetails;

    #[SerializedName('eligible_currency')]
    private ?string $eligibleCurrency = null;

    #[SerializedName('eligible_schedule')]
    private ?string $eligibleSchedule = null;

    private Customer $customer;

    public function getSubscriptionPlans(): array
    {
        return $this->subscriptionPlans;
    }

    public function setSubscriptionPlans(array $subscriptionPlans): void
    {
        $this->subscriptionPlans = $subscriptionPlans;
    }

    public function getPaymentDetails(): array
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(array $paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    public function getEligibleCurrency(): ?string
    {
        return $this->eligibleCurrency;
    }

    public function setEligibleCurrency(?string $eligibleCurrency): void
    {
        $this->eligibleCurrency = $eligibleCurrency;
    }

    public function getEligibleSchedule(): ?string
    {
        return $this->eligibleSchedule;
    }

    public function setEligibleSchedule(?string $eligibleSchedule): void
    {
        $this->eligibleSchedule = $eligibleSchedule;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }
}

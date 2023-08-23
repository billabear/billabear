<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

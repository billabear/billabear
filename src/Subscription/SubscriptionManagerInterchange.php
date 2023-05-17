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

namespace App\Subscription;

use App\Entity\Customer;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanPrice;
use Parthenon\Billing\Subscription\SubscriptionManager;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;

class SubscriptionManagerInterchange implements SubscriptionManagerInterface
{
    public function __construct(
        private SubscriptionManager $stripeBillingManager,
        private InvoiceSubscriptionManager $invoiceSubscriptionManager,
    ) {
    }

    /**
     * @param Customer $customer
     */
    public function startSubscription(CustomerInterface $customer, SubscriptionPlan|Plan $plan, Price|PlanPrice $planPrice, ?PaymentCard $paymentDetails = null, int $seatNumbers = 1, ?bool $hasTrial = null, ?int $trialLengthDays = 0): Subscription
    {
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return $this->invoiceSubscriptionManager->startSubscription($customer, $plan, $planPrice, $paymentDetails, $seatNumbers, $hasTrial, $trialLengthDays);
        }

        return $this->stripeBillingManager->startSubscription($customer, $plan, $planPrice, $paymentDetails, $seatNumbers, $hasTrial, $trialLengthDays);
    }

    public function startSubscriptionWithDto(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription
    {
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return $this->invoiceSubscriptionManager->startSubscriptionWithDto($customer, $startSubscriptionDto);
        }

        return $this->stripeBillingManager->startSubscriptionWithDto($customer, $startSubscriptionDto);
    }

    public function cancelSubscriptionAtEndOfCurrentPeriod(Subscription $subscription): Subscription
    {
        $customer = $subscription->getCustomer();
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return $this->invoiceSubscriptionManager->cancelSubscriptionAtEndOfCurrentPeriod($subscription);
        }

        return $this->stripeBillingManager->cancelSubscriptionAtEndOfCurrentPeriod($subscription);
    }

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription
    {
        $customer = $subscription->getCustomer();
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return $this->invoiceSubscriptionManager->cancelSubscriptionInstantly($subscription);
        }

        return $this->stripeBillingManager->cancelSubscriptionInstantly($subscription);
    }

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTimeInterface $dateTime): Subscription
    {
        $customer = $subscription->getCustomer();
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return $this->invoiceSubscriptionManager->cancelSubscriptionOnDate($subscription);
        }

        return $this->stripeBillingManager->cancelSubscriptionOnDate($subscription);
    }
}

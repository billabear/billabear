<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription;

use App\Subscription\Schedule\SchedulerProvider;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanPrice;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;

class SubscriptionFactory
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private EntityFactoryInterface $entityFactory,
        private SchedulerProvider $schedulerProvider,
    ) {
    }

    public function create(
        CustomerInterface $customer,
        SubscriptionPlan|Plan $plan,
        Price|PlanPrice $planPrice,
        ?PaymentCard $paymentDetails = null,
        ?int $seatNumbers = 1,
        ?bool $hasTrial = null,
        ?int $trialLengthDays = 0,
    ): Subscription {
        if (null === $seatNumbers) {
            $seatNumbers = 1;
        }

        $subscription = $this->entityFactory->getSubscriptionEntity();
        $subscription->setPlanName($plan->getName());
        $subscription->setSubscriptionPlan($plan);
        if ($planPrice->isRecurring()) {
            $subscription->setPaymentSchedule($planPrice->getSchedule());
        } else {
            $subscription->setPaymentSchedule('one-off');
        }
        $subscription->setPrice($planPrice);
        $subscription->setMoneyAmount($planPrice->getAsMoney());
        $subscription->setActive(true);
        $subscription->setStatus(SubscriptionStatus::ACTIVE);
        $subscription->setSeats($seatNumbers);
        $subscription->setCreatedAt(new \DateTime());
        $subscription->setUpdatedAt(new \DateTime());
        $subscription->setStartOfCurrentPeriod(new \DateTime());
        $subscription->setCustomer($customer);
        $subscription->setTrialLengthDays($trialLengthDays ?? $plan->getTrialLengthDays());
        $subscription->setHasTrial($hasTrial ?? $plan->getHasTrial());
        $subscription->setPaymentDetails($paymentDetails);

        $this->schedulerProvider->getScheduler($planPrice)->scheduleNextDueDate($subscription);

        $this->subscriptionRepository->save($subscription);

        return $subscription;
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\CustomerSubscriptionEventRepository;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CustomerEligibleForTrialValidator extends ConstraintValidator
{
    public function __construct(
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private CustomerRepository $customerRepository,
        private CustomerSubscriptionEventRepository $customerSubscriptionEventRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        // Get the subscription plan
        $subscriptionPlanId = $this->context->getObject()->getSubscriptionPlan();
        if (empty($subscriptionPlanId)) {
            return;
        }

        // Check if the subscription is a trial
        $isTrial = $this->context->getObject()->getIsTrial();
        if (!$isTrial) {
            return;
        }

        // Get the subscription plan
        $subscriptionPlan = null;
        if (Uuid::isValid($subscriptionPlanId)) {
            try {
                $subscriptionPlan = $this->subscriptionPlanRepository->getById($subscriptionPlanId);
            } catch (NoEntityFoundException $exception) {
                return;
            }
        } else {
            try {
                /** @var SubscriptionPlan $subscriptionPlan */
                $subscriptionPlan = $this->subscriptionPlanRepository->getByCodeName($subscriptionPlanId);
            } catch (NoEntityFoundException $exception) {
                return;
            }
        }

        // Check if the subscription plan has the "One Per Customer" flag set
        if (!$subscriptionPlan->getIsOnePerCustomer()) {
            return;
        }

        // Get the customer
        $customer = null;
        try {
            $customer = $this->customerRepository->findOneBy(['email' => $value]);
        } catch (NoEntityFoundException $exception) {
            return;
        }

        if (!$customer) {
            return;
        }

        // Check if the customer has already used a trial for this subscription plan
        $events = $this->customerSubscriptionEventRepository->findBy([
            'customer' => $customer,
            'eventType' => CustomerSubscriptionEventType::TRIAL_STARTED,
        ]);

        foreach ($events as $event) {
            $subscription = $event->getSubscription();
            if ($subscription->getSubscriptionPlan()->getId() === $subscriptionPlan->getId()) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }
        }
    }
}

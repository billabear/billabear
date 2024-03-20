<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use App\Dto\Request\Api\Subscription\CreateSubscription;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidPriceValidator extends ConstraintValidator
{
    public function __construct(private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof CreateSubscription) {
            return;
        }
        if (Uuid::isValid((string) $value->getPrice())) {
            return;
        }

        if ($this->context->getViolations()->count() > 0) {
            return;
        }
        try {
            if (Uuid::isValid($value->getSubscriptionPlan())) {
                $subscriptionPlan = $this->subscriptionPlanRepository->findById($value->getSubscriptionPlan());
            } else {
                $subscriptionPlan = $this->subscriptionPlanRepository->getByCodeName($value->getSubscriptionPlan());
            }
        } catch (\Exception $e) {
            return;
        }

        try {
            $subscriptionPlan->getPriceForCurrencyAndSchedule($value->getCurrency(), $value->getSchedule());
        } catch (\Exception $e) {
            $this->context->buildViolation($constraint->message)->atPath('price')->addViolation();
        }
    }
}

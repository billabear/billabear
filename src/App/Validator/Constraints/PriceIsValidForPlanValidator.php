<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use App\Dto\Request\App\Subscription\UpdatePlan;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PriceIsValidForPlanValidator extends ConstraintValidator
{
    public function __construct(
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private PriceRepositoryInterface $priceRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!$value instanceof UpdatePlan) {
            return;
        }

        if (!$value->getPlanId() || !$value->getPriceId()) {
            return;
        }

        try {
            $planId = (string) $this->subscriptionPlanRepository->findById($value->getPlanId())->getProduct()->getId();
            $priceId = (string) $this->priceRepository->findById($value->getPriceId())->getProduct()->getId();

            if ($planId !== $priceId) {
                $this->context->buildViolation($constraint->message)->atPath('price')->addViolation();
            }
        } catch (NoEntityFoundException $e) {
            // These should be caught by other validators.
        }
    }
}

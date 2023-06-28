<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

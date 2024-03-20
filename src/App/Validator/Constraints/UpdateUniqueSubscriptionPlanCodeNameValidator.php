<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use App\Dto\Request\App\Product\UpdateSubscriptionPlan;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UpdateUniqueSubscriptionPlanCodeNameValidator extends ConstraintValidator
{
    public function __construct(private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        $id = null;
        if ($value instanceof UpdateSubscriptionPlan) {
            $id = $value->getId();
            $value = $value->getCodeName();
        }

        if (empty($value)) {
            return;
        }
        try {
            $subscriptionPlan = $this->subscriptionPlanRepository->getByCodeName($value);
        } catch (NoEntityFoundException $exception) {
            return;
        }

        if (isset($id)) {
            if (strval($id) === strval($subscriptionPlan->getId())) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
    }
}

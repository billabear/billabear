<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SubscriptionPlanExistsValidator extends ConstraintValidator
{
    public function __construct(private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (Uuid::isValid($value)) {
            try {
                $this->subscriptionPlanRepository->getById($value);

                return;
            } catch (NoEntityFoundException $exception) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        } else {
            try {
                $this->subscriptionPlanRepository->getByCodeName($value);

                return;
            } catch (NoEntityFoundException $exception) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}

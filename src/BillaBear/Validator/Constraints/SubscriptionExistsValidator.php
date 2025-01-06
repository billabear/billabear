<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SubscriptionExistsValidator extends ConstraintValidator
{
    public function __construct(private SubscriptionRepositoryInterface $subscriptionPlanRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        try {
            $this->subscriptionPlanRepository->getById($value);

            return;
        } catch (NoEntityFoundException $exception) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

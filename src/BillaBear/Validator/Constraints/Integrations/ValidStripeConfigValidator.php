<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\Integrations;

use BillaBear\Dto\Request\App\Settings\Stripe\SendConfig;
use Stripe\Exception\PermissionException;
use Stripe\Stripe;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidStripeConfigValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!$value instanceof SendConfig) {
            return;
        }

        if (!$value->getPrivateKey()) {
            return;
        }

        Stripe::setApiKey($value->getPrivateKey());
        try {
            $account = \Stripe\Account::retrieve();
        } catch (\Stripe\Exception\AuthenticationException|PermissionException $e) {
            $this->context->buildViolation($constraint->message)->atPath('privateKey')->addViolation();
        }
    }
}

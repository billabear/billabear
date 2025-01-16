<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\Integrations;

use BillaBear\Dto\Request\App\Settings\Stripe\SendConfig;
use Parthenon\Common\LoggerAwareTrait;
use Stripe\Balance;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\PermissionException;
use Stripe\Stripe;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidStripeConfigValidator extends ConstraintValidator
{
    use LoggerAwareTrait;

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
            $balance = Balance::retrieve();
            $this->getLogger()->info('Validated a stripe account', ['livemode' => $balance->livemode ? 'live' : 'test']);
        } catch (AuthenticationException|PermissionException $e) {
            $this->context->buildViolation($constraint->message)->atPath('privateKey')->addViolation();
        }
    }
}

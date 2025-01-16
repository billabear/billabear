<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\Integrations;

use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StripeIsConfiguredValidator extends ConstraintValidator
{
    use LoggerAwareTrait;

    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        if (!$settings->getSystemSettings()->getStripePublicKey() || !$settings->getSystemSettings()->getStripePrivateKey()) {
            $this->context->buildViolation($constraint->message)->atPath('stripe')->addViolation();
        }
    }
}

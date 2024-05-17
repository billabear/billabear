<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\Integrations;

use BillaBear\Enum\SlackNotificationEvent;
use BillaBear\Repository\SlackWebhookRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidSlackEventValidator extends ConstraintValidator
{
    public function __construct(private SlackWebhookRepositoryInterface $slackWebhookRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        try {
            SlackNotificationEvent::from($value);
        } catch (\Throwable $exception) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        }
    }
}

<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\Integrations;

use BillaBear\Repository\SlackWebhookRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SlackWebhookUniqueValidator extends ConstraintValidator
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
            $this->slackWebhookRepository->getByName($value);
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        } catch (NoEntityFoundException $exception) {
        }
    }
}

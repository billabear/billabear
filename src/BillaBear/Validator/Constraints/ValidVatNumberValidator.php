<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use BillaBear\Exception\Tax\VatSense\FailedRequestException;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Tax\VatSense\VatSenseClient;
use GuzzleHttp\Exception\BadResponseException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidVatNumberValidator extends ConstraintValidator
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository, private VatSenseClient $vatSenseClient)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        $taxSettings = $this->settingsRepository->getDefaultSettings()->getTaxSettings();
        if (!$taxSettings->getVatSenseEnabled()) {
            return;
        }

        if (!$taxSettings->getValidateTaxNumber()) {
            return;
        }

        try {
            if ($this->vatSenseClient->validateTaxId($value)) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        } catch (FailedRequestException $exception) {
            /** @var BadResponseException $previous */
            $previous = $exception->getPrevious();

            if (400 === $previous->getResponse()->getStatusCode()) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }

            $this->context->buildViolation(sprintf($constraint->error, $exception->getPrevious()?->getMessage()))->addViolation();
        }
    }
}

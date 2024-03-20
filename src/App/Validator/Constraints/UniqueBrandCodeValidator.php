<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use App\Repository\BrandSettingsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueBrandCodeValidator extends ConstraintValidator
{
    public function __construct(private BrandSettingsRepositoryInterface $brandSettingRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        try {
            $this->brandSettingRepository->getByCode($value);
        } catch (NoEntityFoundException $exception) {
            return;
        }

        $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
    }
}

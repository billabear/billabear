<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use BillaBear\Repository\VoucherRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VoucherCodeExistsValidator extends ConstraintValidator
{
    public function __construct(private VoucherRepositoryInterface $voucherRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        try {
            $this->voucherRepository->getActiveByCode($value);

            return;
        } catch (NoEntityFoundException $exception) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

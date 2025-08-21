<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InTheFutureValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        $datatime = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $value);
        $now = new \DateTime();
        if ($now >= $datatime) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

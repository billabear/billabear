<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use App\Dto\Request\App\CreateProduct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductHasTaxValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof CreateProduct && !$value instanceof \App\Dto\Request\Api\CreateProduct) {
            return;
        }

        if (empty($value->getTaxType()) && ($value instanceof CreateProduct && empty($value->getTaxRate()))) {
            $this->context->buildViolation($constraint->message)->atPath('taxType')->addViolation();
        }
    }
}

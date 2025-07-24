<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints;

use BillaBear\Dto\Request\App\CreateProduct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductHasTaxValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof CreateProduct && !$value instanceof \BillaBear\Dto\Request\Api\CreateProduct) {
            return;
        }

        if (empty($value->tax_type) && ($value instanceof CreateProduct && empty($value->taxRate))) {
            $this->context->buildViolation($constraint->message)->atPath('taxType')->addViolation();
        }
    }
}

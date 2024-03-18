<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Validator\Constraints;

use App\Repository\TaxTypeRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxTypeExistsValidator extends ConstraintValidator
{
    public function __construct(private TaxTypeRepositoryInterface $taxTypeRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        try {
            $targetPrice = $this->taxTypeRepository->findById($value);
        } catch (NoEntityFoundException $exception) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        }
    }
}

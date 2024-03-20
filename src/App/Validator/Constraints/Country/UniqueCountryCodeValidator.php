<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Validator\Constraints\Country;

use App\Repository\CountryRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCountryCodeValidator extends ConstraintValidator
{
    public function __construct(private CountryRepositoryInterface $countryRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        try {
            $this->countryRepository->getByIsoCode($value);
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        } catch (NoEntityFoundException $exception) {
        }
    }
}

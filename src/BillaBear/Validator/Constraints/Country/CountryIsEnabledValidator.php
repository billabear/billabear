<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\Country;

use BillaBear\Repository\CountryRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CountryIsEnabledValidator extends ConstraintValidator
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
            $country = $this->countryRepository->getByIsoCode($value);
            if (!$country->isEnabled()) {
                $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
            }
        } catch (NoEntityFoundException $exception) {
        }
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\EconomicArea;

use BillaBear\Dto\Request\App\EconomicArea\CreateMembership;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\EconomicAreaRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotMemberOfEconomicAreaValidator extends ConstraintValidator
{
    public function __construct(
        private EconomicAreaRepositoryInterface $economicAreaRepository,
        private CountryRepositoryInterface $countryRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!$value instanceof CreateMembership) {
            return;
        }
    }
}

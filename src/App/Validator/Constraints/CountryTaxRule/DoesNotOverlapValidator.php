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

namespace App\Validator\Constraints\CountryTaxRule;

use App\Dto\Request\App\Country\CreateCountryTaxRule;
use App\Repository\CountryRepositoryInterface;
use App\Repository\CountryTaxRuleRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DoesNotOverlapValidator extends ConstraintValidator
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof CreateCountryTaxRule) {
            return;
        }
        if (!$value->getValidFrom()) {
            return;
        }

        $country = $this->countryRepository->getById($value->getCountry());
        $countryTaxRules = $this->countryTaxRuleRepository->getForCountry($country);
        $validFrom = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $value->getValidFrom());

        foreach ($countryTaxRules as $countryTaxRule) {
            if ($countryTaxRule->getValidFrom() < $validFrom && (null === $countryTaxRule->getValidUntil() || $countryTaxRule->getValidUntil() > $validFrom)) {
                $this->context->buildViolation($constraint->message)->atPath('validFrom')->addViolation();

                return;
            }

            if ($value->getValidUntil()) {
                $validUntil = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $value->getValidUntil());
                if ($countryTaxRule->getValidFrom() > $validUntil && (null === $countryTaxRule->getValidUntil() || $countryTaxRule->getValidUntil() < $validUntil)) {
                    $this->context->buildViolation($constraint->message)->atPath('validUntil')->addViolation();

                    return;
                }
            }
        }
    }
}

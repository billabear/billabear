<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\CountryTaxRule;

use BillaBear\Dto\Request\App\Country\CreateCountryTaxRule;
use BillaBear\Dto\Request\App\Country\UpdateCountryTaxRule;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\CountryTaxRuleRepositoryInterface;
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
        if (!$value instanceof CreateCountryTaxRule && !$value instanceof UpdateCountryTaxRule) {
            return;
        }
        if (!$value->getValidFrom()) {
            return;
        }
        if (!$value->getCountry()) {
            return;
        }

        $originalRule = null;
        if ($value instanceof UpdateCountryTaxRule) {
            $originalRule = $this->countryTaxRuleRepository->findById($value->getId());
        }
        $country = $this->countryRepository->getById($value->getCountry());
        $countryTaxRules = $this->countryTaxRuleRepository->getForCountry($country);
        $validFrom = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $value->getValidFrom());

        foreach ($countryTaxRules as $countryTaxRule) {
            if (isset($originalRule) && strval($countryTaxRule->getId()) === strval($originalRule->getId())) {
                continue;
            }
            if ($countryTaxRule->getValidFrom() < $validFrom && (null !== $countryTaxRule->getValidUntil() || $countryTaxRule->getValidUntil() > $validFrom)) {
                $this->context->buildViolation($constraint->message)->atPath('validFrom')->addViolation();

                return;
            }

            if ($value->getValidUntil()) {
                $validUntil = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $value->getValidUntil());
                if ($countryTaxRule->getValidFrom() < $validUntil && (null !== $countryTaxRule->getValidUntil() || $countryTaxRule->getValidUntil() > $validUntil)) {
                    $this->context->buildViolation($constraint->message)->atPath('validUntil')->addViolation();

                    return;
                }
            }
        }
    }
}

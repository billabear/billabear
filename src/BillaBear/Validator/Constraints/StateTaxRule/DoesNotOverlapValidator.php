<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Validator\Constraints\StateTaxRule;

use BillaBear\Dto\Request\App\Country\CreateStateTaxRule;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use BillaBear\Repository\StateTaxRuleRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DoesNotOverlapValidator extends ConstraintValidator
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private StateRepositoryInterface $stateRepository,
        private StateTaxRuleRepositoryInterface $stateTaxRuleRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof CreateStateTaxRule) {
            return;
        }
        if (!$value->getValidFrom()) {
            return;
        }
        if (!$value->getCountry()) {
            return;
        }

        $originalRule = null;
        /*
        if ($value instanceof Update) {
            $originalRule = $this->stateTaxRuleRepository->findById($value->getId());
        }*/

        $country = $this->countryRepository->getById($value->getCountry());
        $state = $this->stateRepository->getById($value->getState());
        $stateTaxRules = $this->stateTaxRuleRepository->getForState($state);

        $validFrom = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $value->getValidFrom());

        foreach ($stateTaxRules as $stateTaxRule) {
            if (isset($originalRule) && strval($stateTaxRule->getId()) === strval($originalRule->getId())) {
                continue;
            }
            if ($stateTaxRule->getValidFrom() < $validFrom && (null !== $stateTaxRule->getValidUntil() || $stateTaxRule->getValidUntil() > $validFrom)) {
                $this->context->buildViolation($constraint->message)->atPath('validFrom')->addViolation();

                return;
            }

            if ($value->getValidUntil()) {
                $validUntil = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $value->getValidUntil());
                if ($stateTaxRule->getValidFrom() < $validUntil && (null !== $stateTaxRule->getValidUntil() || $stateTaxRule->getValidUntil() > $validUntil)) {
                    $this->context->buildViolation($constraint->message)->atPath('validUntil')->addViolation();

                    return;
                }
            }
        }
    }
}

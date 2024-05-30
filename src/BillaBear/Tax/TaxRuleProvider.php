<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
use BillaBear\Exception\NoRateForCountryException;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\CountryTaxRuleRepositoryInterface;
use BillaBear\Repository\StateTaxRuleRepositoryInterface;
use Brick\Money\Money;
use Parthenon\Common\Address;
use Parthenon\Common\Exception\NoEntityFoundException;

class TaxRuleProvider
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
        private StateTaxRuleRepositoryInterface $stateTaxRuleRepository,
        private ThresholdManager $thresholdManager,
    ) {
    }

    public function getCountryRule(TaxType $taxType, Address $address, ?\DateTime $when = null): CountryTaxRule
    {
        if (!$when) {
            $when = new \DateTime();
        }
        try {
            $country = $this->countryRepository->getByIsoCode($address->getCountry());
        } catch (NoEntityFoundException $entityFoundException) {
            throw new NoRateForCountryException(sprintf('No country entity found for %s', $address->getCountry()), previous: $entityFoundException);
        }
        $rules = $this->countryTaxRuleRepository->getForCountryAndTaxType($country, $taxType);

        foreach ($rules as $rule) {
            if ($rule->isValidForDateTime($when)) {
                return $rule;
            }
        }
        $default = $this->countryTaxRuleRepository->getDefaultForCountryAndTaxType($country, $taxType);

        if ($default) {
            return $default;
        }

        throw new NoRateForCountryException(sprintf("No tax rate for '%s' found", $address->getCountry()));
    }

    public function getStateRule(TaxType $taxType, Address $address, ?Money $amount, ?\DateTime $when = null): ?StateTaxRule
    {
        if (!$address->getRegion()) {
            return null;
        }

        if (!$when) {
            $when = new \DateTime();
        }
        try {
            $country = $this->countryRepository->getByIsoCode($address->getCountry());
        } catch (NoEntityFoundException $entityFoundException) {
            return null;
        }

        if ($country->getStates()->isEmpty()) {
            return null;
        }

        $stateName = strtolower($address->getRegion());

        $foundState = null;
        foreach ($country->getStates() as $state) {
            if (strtolower($state->getName()) === $stateName) {
                $foundState = $state;
                break;
            }
            if (strtolower($state->getCode()) === $stateName) {
                $foundState = $state;
                break;
            }
        }

        if (!$foundState instanceof State) {
            return null;
        }

        if (!$this->thresholdManager->isThresholdReachedForState($country->getIsoCode(), $foundState, $amount) && !$foundState->hasNexus()) {
            return null;
        }

        $rules = $this->stateTaxRuleRepository->getForCountryStateAndTaxType($country, $state, $taxType);

        foreach ($rules as $rule) {
            if ($rule->isValidForDateTime($when)) {
                return $rule;
            }
        }
        $default = $this->stateTaxRuleRepository->getDefaultForCountryStateAndTaxType($country, $state, $taxType);

        if ($default) {
            return $default;
        }

        return null;
    }
}

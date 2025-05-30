<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
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

    public function getCountryRule(TaxType $taxType, Address $address, ?\DateTime $when = null): ?CountryTaxRule
    {
        if (!$when) {
            $when = new \DateTime();
        }
        try {
            $country = $this->countryRepository->getByIsoCode($address->getCountry());
        } catch (NoEntityFoundException $entityFoundException) {
            return null;
        }
        $rules = $this->countryTaxRuleRepository->getForCountryAndTaxType($country, $taxType);

        foreach ($rules as $rule) {
            if ($rule->isValidForDateTime($when)) {
                return $rule;
            }
        }

        return $this->countryTaxRuleRepository->getDefaultForCountryAndTaxType($country, $taxType);
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

        if (!$this->thresholdManager->isThresholdReachedForState($country->getIsoCode(), $foundState, $amount) && !$foundState->isCollecting()) {
            return null;
        }

        $rules = $this->stateTaxRuleRepository->getForCountryStateAndTaxType($state, $taxType);

        foreach ($rules as $rule) {
            if ($rule->isValidForDateTime($when)) {
                return $rule;
            }
        }
        $default = $this->stateTaxRuleRepository->getDefaultForCountryStateAndTaxType($state, $taxType);

        if ($default) {
            return $default;
        }

        return null;
    }
}

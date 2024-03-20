<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tax;

use App\Entity\CountryTaxRule;
use App\Entity\TaxType;
use App\Exception\NoRateForCountryException;
use App\Repository\CountryRepositoryInterface;
use App\Repository\CountryTaxRuleRepositoryInterface;
use Parthenon\Common\Address;
use Parthenon\Common\Exception\NoEntityFoundException;

class TaxRuleProvider
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
    ) {
    }

    public function getRule(TaxType $taxType, Address $address, ?\DateTime $when = null): CountryTaxRule
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
        $default = null;
        foreach ($rules as $rule) {
            if ($rule->isIsDefault()) {
                $default = $rule;
            }
            if ($rule->isValidForDateTime($when)) {
                return $rule;
            }
        }

        if ($default) {
            return $rule;
        }

        throw new NoRateForCountryException('No tax rule found');
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tax;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\TaxType;
use App\Enum\CustomerType;
use App\Exception\NoRateForCountryException;
use App\Repository\SettingsRepositoryInterface;

class TaxRateProvider implements TaxRateProviderInterface
{
    public function __construct(
        private TaxRuleProvider $taxRuleProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function getRateForCustomer(Customer $customer, TaxType $taxType, ?Product $product = null): TaxInfo
    {
        if ($product && null !== $product->getTaxRate()) {
            return new TaxInfo($product->getTaxRate(), $customer->getBillingAddress()->getCountry(), false);
        }

        $physical = ($product && $product->getPhysical());

        if ($customer->getDigitalTaxRate() && !$physical) {
            return new TaxInfo($customer->getDigitalTaxRate(), $customer->getBillingAddress()->getCountry(), false);
        }

        if ($customer->getStandardTaxRate()) {
            return new TaxInfo($customer->getStandardTaxRate(), $customer->getBillingAddress()->getCountry(), false);
        }

        $taxCustomersWithTaxNumbers = $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getTaxCustomersWithTaxNumbers();

        try {
            $taxRule = $this->taxRuleProvider->getRule($taxType, $customer->getBillingAddress());
            $customerTaxRate = $taxRule->getTaxRate();
            $customerTaxCountry = $customer->getBillingAddress()->getCountry();
        } catch (NoRateForCountryException $e) {
            try {
                $taxRule = $this->taxRuleProvider->getRule($taxType, $customer->getBillingAddress());
                $customerTaxRate = $taxRule->getTaxRate();
            } catch (NoRateForCountryException $e) {
                if (!$physical) {
                    $customerTaxRate = $customer->getBrandSettings()->getDigitalServicesRate();
                }
                if (!isset($customerTaxRate)) {
                    $customerTaxRate = $customer->getBrandSettings()->getTaxRate();
                }
            }
            $customerTaxCountry = $customer->getBrandSettings()->getAddress()->getCountry();
        }

        $euBusinessTaxRules = $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getEuropeanBusinessTaxRules();
        if ($euBusinessTaxRules && CustomerType::BUSINESS === $customer->getType() && (isset($taxRule) && $taxRule->getCountry()->isInEu())) {
            if (!$physical) {
                return new TaxInfo(0, $customer->getBillingAddress()->getCountry(), false);
            } else {
                return new TaxInfo($customerTaxRate, $customer->getBillingAddress()->getCountry(), true);
            }
        }

        if (!$taxCustomersWithTaxNumbers && $customer->getTaxNumber()) {
            return new TaxInfo(null, $customerTaxCountry, false);
        }

        return new TaxInfo($customerTaxRate, $customerTaxCountry, false);
    }
}

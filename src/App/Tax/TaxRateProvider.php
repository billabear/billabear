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

namespace App\Tax;

use App\Entity\Customer;
use App\Entity\Product;
use App\Enum\CustomerType;
use App\Enum\TaxType;
use App\Exception\NoRateForCountryException;
use App\Repository\SettingsRepositoryInterface;

class TaxRateProvider implements TaxRateProviderInterface
{
    public function __construct(
        private CountryRules $countryRules,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function getRateForCustomer(Customer $customer, TaxType $taxType, ?Product $product = null): TaxInfo
    {
        if ($product && null !== $product->getTaxRate()) {
            return new TaxInfo($product->getTaxRate(), $customer->getBillingAddress()->getCountry(), false);
        }

        if ($customer->getDigitalTaxRate() && TaxType::DIGITAL_SERVICES === $taxType) {
            return new TaxInfo($customer->getDigitalTaxRate(), $customer->getBillingAddress()->getCountry(), false);
        }
        if ($customer->getStandardTaxRate()) {
            return new TaxInfo($customer->getStandardTaxRate(), $customer->getBillingAddress()->getCountry(), false);
        }

        $taxCustomersWithTaxNumbers = $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getTaxCustomersWithTaxNumbers();

        try {
            $customerTaxRate = $this->countryRules->getDigitalVatPercentage($customer->getBillingAddress());
            $customerTaxCountry = $customer->getBillingAddress()->getCountry();
        } catch (NoRateForCountryException $e) {
            try {
                $customerTaxRate = $this->countryRules->getDigitalVatPercentage($customer->getBrandSettings()->getAddress());
            } catch (NoRateForCountryException $e) {
                if (TaxType::DIGITAL_SERVICES === $taxType) {
                    $customerTaxRate = $customer->getBrandSettings()->getDigitalServicesRate();
                }
                if (!isset($customerTaxRate)) {
                    $customerTaxRate = $customer->getBrandSettings()->getTaxRate();
                }
            }
            $customerTaxCountry = $customer->getBrandSettings()->getAddress()->getCountry();
        }
        $euBusinessTaxRules = $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getEuropeanBusinessTaxRules();
        if ($euBusinessTaxRules && CustomerType::BUSINESS === $customer->getType() && $this->countryRules->inEu($customer->getBillingAddress())) {
            if (TaxType::PHYSICAL === $taxType) {
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

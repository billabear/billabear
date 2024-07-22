<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Product;
use BillaBear\Entity\TaxType;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Brick\Money\Money;
use Parthenon\Common\Address;

class TaxRateProvider implements TaxRateProviderInterface
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private TaxRuleProvider $taxRuleProvider,
        private ThresholdManager $thresholdManager,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function getRateForCustomer(Customer $customer, ?TaxType $taxType, ?Product $product = null, ?Money $amount = null): TaxInfo
    {
        $customerCountry = $customer->getCountry();
        if ($customer->hasStandardTaxRate()) {
            return new TaxInfo($customer->getStandardTaxRate(), $customerCountry, false);
        }

        $brand = $customer->getBrandSettings();
        $brandCountry = $brand->getAddress()->getCountry();
        $physical = ($product && $product->getPhysical());

        if ($product && $product->getTaxRate()) {
            return new TaxInfo($product->getTaxRate(), $brandCountry, false);
        }

        if ($brand->getTaxRate()) {
            return new TaxInfo($brand->getTaxRate(), $brandCountry, false);
        }

        if (!$taxType) {
            throw new \Exception('Must have tax type set');
        }

        if (!$this->countryRepository->hasWithIsoCode($customerCountry)) {
            return $this->buildTaxInfo($taxType, $brand->getAddress(), $amount);
        }

        $customerCountry = $this->countryRepository->getByIsoCode($customer->getCountry());
        $taxAddress = $customer->getBillingAddress();

        if ($this->areBothPartiesInTheEU($customer->getBillingAddress(), $brand->getAddress())) {
            $reverseCharge = false;

            if ($customer->isBusiness() && $customer->getTaxNumber()) {
                if ($physical) {
                    $reverseCharge = true;
                } else {
                    return new TaxInfo(0, $taxAddress->getCountry(), false);
                }
            }
            if (!$this->thresholdManager->isThresholdReached($taxAddress->getCountry(), $amount)) {
                $taxAddress = $brand->getAddress();
            }

            return $this->buildTaxInfo($taxType, $taxAddress, $amount, $reverseCharge);
        }

        $taxCustomersWithTaxNumbers = $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getTaxCustomersWithTaxNumbers();

        if (!$taxCustomersWithTaxNumbers && $customer->getTaxNumber()) {
            return new TaxInfo(0, $customer->getBillingAddress()->getCountry(), false);
        }

        if (!$this->thresholdManager->isThresholdReached($taxAddress->getCountry(), $amount)) {
            $taxAddress = $brand->getAddress();
        }

        return $this->buildTaxInfo($taxType, $taxAddress, $amount);
    }

    public function areBothPartiesInTheEU(Address $customerAddress, Address $brandAddress): bool
    {
        $customerCountry = $this->countryRepository->getByIsoCode($customerAddress->getCountry());
        $brandCountry = $this->countryRepository->getByIsoCode($brandAddress->getCountry());

        return $customerCountry->isInEu() && $brandCountry->isInEu();
    }

    public function buildTaxInfo(TaxType $taxType, Address $address, Money $amount, bool $reverseCharge = false): TaxInfo
    {
        $countryCode = $address->getCountry();

        if (!$this->thresholdManager->isThresholdReached($countryCode, $amount)) {
            return new TaxInfo(null, null, false);
        }

        $countryTax = $this->taxRuleProvider->getCountryRule($taxType, $address);
        $stateTax = $this->taxRuleProvider->getStateRule($taxType, $address, $amount);

        $state = null;
        $taxRate = $countryTax?->getTaxRate();

        if ($stateTax) {
            $state = ucwords($address->getRegion());
            $taxRate += $stateTax->getTaxRate();
        }

        return new TaxInfo($taxRate, $countryCode, $reverseCharge, $state);
    }
}

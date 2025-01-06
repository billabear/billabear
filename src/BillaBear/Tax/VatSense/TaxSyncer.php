<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax\VatSense;

use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\TaxType;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\CountryTaxRuleRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;

class TaxSyncer
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private VatSenseClient $vatSenseClient,
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private CountryRepositoryInterface $countryRepository,
        private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
    ) {
    }

    public function process(): void
    {
        $taxSettings = $this->settingsRepository->getDefaultSettings()->getTaxSettings();

        if (!$taxSettings->getVatSenseEnabled()) {
            $this->getLogger()->info('Vat Sense is not enabled');

            return;
        }

        $this->getLogger()->info('Vat Sense is enabled and starting the syncing process');

        $taxTypes = $this->taxTypeRepository->getAll();
        $taxRates = $this->vatSenseClient->getTaxRates();
        $countries = $this->countryRepository->getAll();

        foreach ($taxTypes as $taxType) {
            if (null === $taxType->getVatSenseType()) {
                $this->getLogger()->info('Vat Sense type is not set', ['tax_type' => $taxType->getName()]);
                continue;
            }

            foreach ($countries as $country) {
                $rates = $this->findRates($taxRates, $country, $taxType);
                if (null === $rates) {
                    $this->getLogger()->info('Skipping country because country is not found in VAT Sense data', ['country' => $country->getName()]);
                    continue;
                }
                $newRate = BigDecimal::of($rates['rate']);

                try {
                    $taxRule = $this->countryTaxRuleRepository->getOpenEndedForCountryAndTaxType($country, $taxType);
                } catch (NoEntityFoundException) {
                    $this->getLogger()->info('Creating new rule because no rule existed', ['country' => $country->getName()]);
                    $newTaxRule = $this->buildNewRule($country, $newRate, $taxType);

                    $this->countryTaxRuleRepository->save($newTaxRule);
                    continue;
                }

                if ($taxRule->startsInFuture()) {
                    $this->getLogger()->info('Skipping country because current open-ended tax rule is for the future', ['country' => $country->getName()]);

                    continue;
                }

                $oldRate = BigDecimal::of($taxRule->getTaxRate());
                if ($newRate->isEqualTo($oldRate)) {
                    $this->getLogger()->info('Skipping country because tax rate is the same', ['country' => $country->getName()]);

                    continue;
                }

                $newTaxRule = $this->buildNewRule($country, $newRate, $taxType, $taxRule);

                $taxRule->setValidUntil(new \DateTime('-2 seconds'));
                $taxRule->setIsDefault(false);

                $this->countryTaxRuleRepository->save($taxRule);
                $this->countryTaxRuleRepository->save($newTaxRule);
            }
        }
    }

    public function findRates(array $taxRates, Country $country, TaxType $taxType): ?array
    {
        foreach ($taxRates as $taxRate) {
            if (strtolower($taxRate['country_code']) === strtolower($country->getIsoCode())) {
                foreach ($taxRate['other'] ?? [] as $rate) {
                    if (
                        false !== stripos((string) $rate['description'], $taxType->getVatSenseType())
                        || false !== stripos((string) $rate['types'], $taxType->getVatSenseType())
                    ) {
                        return $rate;
                    }
                }

                return $taxRate['standard'];
            }
        }

        return null;
    }

    public function buildNewRule(Country $country, BigDecimal|BigNumber $newRate, TaxType $taxType, ?CountryTaxRule $taxRule = null): CountryTaxRule
    {
        $newTaxRule = new CountryTaxRule();
        $newTaxRule->setCountry($country);
        $newTaxRule->setTaxRate($newRate->toFloat());
        $newTaxRule->setTaxType($taxType);
        $newTaxRule->setCreatedAt(new \DateTime());
        $newTaxRule->setValidFrom(new \DateTime());
        $newTaxRule->setIsDefault($taxRule?->isIsDefault() ?? true);

        return $newTaxRule;
    }
}

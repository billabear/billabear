<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax\Report;

use BillaBear\DataMappers\Tax\CountryDataMapper;
use BillaBear\Dto\Response\App\Tax\ActiveTaxCountries;
use BillaBear\Entity\Country;
use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\TaxReportRepositoryInterface;
use BillaBear\Tax\ThresholdManager;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class ActiveCountryProvider
{
    private CurrencyConverter $currencyConverter;

    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private ThresholdManager $thresholdManager,
        private CountryDataMapper $countryDataMapper,
        private BricksExchangeRateProvider $exchangeRateProvider,
        private TaxReportRepositoryInterface $taxReportRepository,
    ) {
        $this->currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
    }

    /**
     * @return ActiveTaxCountries[]
     */
    public function getActiveCountries(?\DateTime $when = null): array
    {
        $countries = $this->countryRepository->getAll();
        $output = [];

        foreach ($countries as $country) {
            $transactedAmount = $this->thresholdManager->getTransactedAmount($country, $when);
            if (!$transactedAmount->isPositive()) {
                continue;
            }

            $activeCountry = new ActiveTaxCountries();
            $activeCountry->setThresholdAmount($country->getThreshold());
            $activeCountry->setTransactedAmount($transactedAmount->getMinorAmount()->toInt());
            $activeCountry->setCountry($this->countryDataMapper->createAppDto($country));
            $activeCountry->setThresholdReached($this->thresholdManager->isThresholdReached($country->getIsoCode(), Money::zero('USD')));
            $activeCountry->setCollectedAmount($this->getAmountCollected($country, $when));

            $output[] = $activeCountry;
        }

        return $output;
    }

    private function getAmountCollected(Country $country, ?\DateTime $when): int
    {
        $collectedAmounts = $this->taxReportRepository->getTaxCollected($country->getIsoCode(), $when);

        $defaultCurrency = $country->getCurrency();
        $money = Money::zero($defaultCurrency);

        foreach ($collectedAmounts as $amountData) {
            $originalFee = Money::ofMinor($amountData['amount'], $amountData['currency']);
            $amountToAdd = $this->currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        return $money->getMinorAmount()->toInt();
    }
}

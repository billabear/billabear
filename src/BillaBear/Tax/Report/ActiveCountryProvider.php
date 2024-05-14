<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax\Report;

use BillaBear\DataMappers\CountryDataMapper;
use BillaBear\Dto\Response\App\Tax\ActiveTaxCountries;
use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Tax\ThresholdManager;
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

            $output[] = $activeCountry;
        }

        return $output;
    }
}

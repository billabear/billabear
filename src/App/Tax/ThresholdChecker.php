<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tax;

use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\CountryRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class ThresholdChecker
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function isThresholdReached(string $countryCode, Money $money): bool
    {
        $defaultCurrency = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency();
        $currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
        $money = Money::zero($defaultCurrency);
        $amounts = $this->paymentRepository->getPaymentsAmountForCountrySinceDate($countryCode, new \DateTime('-12 months'));

        foreach ($amounts as $amountData) {
            $originalFee = Money::of($amountData['amount'], $amountData['currency']);
            $amountToAdd = $currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        $amountToAdd = $currencyConverter->convert($money, $defaultCurrency, RoundingMode::HALF_DOWN);
        $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);

        $country = $this->countryRepository->getByIsoCode($countryCode);

        return $country->getThresholdAsMoney()->isLessThanOrEqualTo($money);
    }
}

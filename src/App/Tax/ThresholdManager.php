<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tax;

use App\Entity\Country;
use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\CountryRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;
use Parthenon\Common\Exception\NoEntityFoundException;

class ThresholdManager
{
    private CurrencyConverter $currencyConverter;

    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private BricksExchangeRateProvider $exchangeRateProvider,
    ) {

        $this->currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
    }

    public function isThresholdReached(string $countryCode, Money $money): bool
    {
        try {
            $country = $this->countryRepository->getByIsoCode($countryCode);

            $amountTransacted = $this->getThreshold($country);

            $amountToAdd = $this->currencyConverter->convert($money, $country->getCurrency(), RoundingMode::HALF_DOWN);
            $amountTransacted = $amountTransacted->plus($amountToAdd, RoundingMode::HALF_DOWN);

            return $country->getThresholdAsMoney()->isLessThanOrEqualTo($amountTransacted);
        } catch (NoEntityFoundException) {
            return false;
        }
    }

    public function getThreshold(Country $country): Money
    {
        $defaultCurrency = $country->getCurrency();
        $money = Money::zero($defaultCurrency);
        $amounts = $this->paymentRepository->getPaymentsAmountForCountrySinceDate($country->getIsoCode(), new \DateTime('-12 months'));

        foreach ($amounts as $amountData) {
            $originalFee = Money::of($amountData['amount'], $amountData['currency']);
            $amountToAdd = $this->currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }
        return $money;
    }
}

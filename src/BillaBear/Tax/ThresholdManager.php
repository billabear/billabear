<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\Country;
use BillaBear\Entity\State;
use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\PaymentRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;
use Parthenon\Common\Exception\NoEntityFoundException;

class ThresholdManager
{
    private CurrencyConverter $currencyConverter;

    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private StateRepositoryInterface $stateRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
    }

    public function isThresholdReached(string $countryCode, ?Money $money): bool
    {
        if (!$money) {
            return false;
        }
        try {
            $country = $this->countryRepository->getByIsoCode($countryCode);

            if ($country->getCollecting()) {
                return true;
            }

            if ($country->isInEu() && $this->isEuStopShopEnabled()) {
                return true;
            }

            $amountTransacted = $this->getTransactedAmount($country);

            $amountToAdd = $this->currencyConverter->convert($money, $country->getCurrency(), RoundingMode::HALF_DOWN);
            $amountTransacted = $amountTransacted->plus($amountToAdd, RoundingMode::HALF_DOWN);

            $returnValue = $country->getThresholdAsMoney()->isLessThanOrEqualTo($amountTransacted);

            if ($returnValue) {
                $country->setCollecting(true);
                $this->countryRepository->save($country);
            }

            return $returnValue;
        } catch (NoEntityFoundException) {
            return false;
        }
    }

    public function getTransactedAmount(Country $country, ?\DateTime $when = null): Money
    {
        if (!$when) {
            $when = new \DateTime('-12 months');
        }
        $defaultCurrency = $country->getCurrency();
        $money = Money::zero($defaultCurrency);
        $amounts = $this->paymentRepository->getPaymentsAmountForCountrySinceDate($country->getIsoCode(), $when);

        foreach ($amounts as $amountData) {
            $originalFee = Money::ofMinor($amountData['amount'], $amountData['currency']);
            $amountToAdd = $this->currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        return $money;
    }

    public function isThresholdReachedForState(string $countryCode, State $state, ?Money $money): bool
    {
        if (!$money) {
            return false;
        }

        if ($state->isCollecting()) {
            return true;
        }

        try {
            $country = $this->countryRepository->getByIsoCode($countryCode);

            $amountTransacted = $this->getTransactedAmountForState($country, $state);

            $amountToAdd = $this->currencyConverter->convert($money, $country->getCurrency(), RoundingMode::HALF_DOWN);
            $amountTransacted = $amountTransacted->plus($amountToAdd, RoundingMode::HALF_DOWN);

            $stateThreshold = Money::ofMinor($state->getThreshold(), $country->getCurrency());

            $returnValue = $stateThreshold->isLessThanOrEqualTo($amountTransacted);
            if ($returnValue) {
                $state->setCollecting(true);
                $this->stateRepository->save($state);
            }

            return $returnValue;
        } catch (NoEntityFoundException) {
            return false;
        }
    }

    public function getTransactedAmountForState(Country $country, State $state, ?\DateTime $when = null): Money
    {
        if (!$when) {
            $when = new \DateTime('-12 months');
        }
        $defaultCurrency = $country->getCurrency();
        $money = Money::zero($defaultCurrency);
        $amounts = $this->paymentRepository->getPaymentsAmountForStateSinceDate($country->getIsoCode(), $state->getName(), $when);

        foreach ($amounts as $amountData) {
            $originalFee = Money::ofMinor($amountData['amount'], $amountData['currency']);
            $amountToAdd = $this->currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        return $money;
    }

    protected function isEuStopShopEnabled(): bool
    {
        return $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getOneStopShopTaxRules();
    }
}

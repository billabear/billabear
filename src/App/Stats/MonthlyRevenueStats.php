<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Stats;

use App\Entity\Stats\CachedStats;
use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\Stats\CachedStatsRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class MonthlyRevenueStats
{
    public function __construct(
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private CachedStatsRepositoryInterface $cachedStatsRepository
    ) {
    }

    public function adjustStats(): void
    {
        $subscriptions = $this->subscriptionRepository->getAllActive();

        $defaultCurrency = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency();
        $currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
        $money = Money::zero($defaultCurrency);

        foreach ($subscriptions as $subscription) {
            if (!$subscription->getPaymentSchedule()) {
                continue;
            }
            if ('year' === $subscription->getPaymentSchedule()) {
                continue;
            }
            $originalFee = $subscription->getMoneyAmount();
            if ('week' === $subscription->getPaymentSchedule()) {
                $originalFee = $subscription->getMoneyAmount()->multipliedBy(4, RoundingMode::HALF_DOWN);
            }

            $amountToAdd = $currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        $stat = $this->cachedStatsRepository->getMoneyStat(CachedStats::STAT_NAME_ESTIMATED_MRR);
        $stat->setValue($money->getMinorAmount()->toInt());
        $this->cachedStatsRepository->save($stat);
    }
}

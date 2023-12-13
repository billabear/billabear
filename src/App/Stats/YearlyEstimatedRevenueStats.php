<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

class YearlyEstimatedRevenueStats
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
            $originalFee = match ($subscription->getPaymentSchedule()) {
                'week' => $subscription->getMoneyAmount()->multipliedBy(52, RoundingMode::HALF_DOWN),
                'month' => $subscription->getMoneyAmount()->multipliedBy(12, RoundingMode::HALF_DOWN),
                default => $subscription->getMoneyAmount(),
            };

            $amountToAdd = $currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        $stat = $this->cachedStatsRepository->getMoneyStat(CachedStats::STAT_NAME_ESTIMATED_ARR);
        $stat->setValue($money->getMinorAmount()->toInt());
        $this->cachedStatsRepository->save($stat);
    }
}

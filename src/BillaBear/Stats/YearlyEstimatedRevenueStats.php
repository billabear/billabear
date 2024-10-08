<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Stats\CachedStats;
use BillaBear\Invoice\Usage\CostEstimator;
use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\Stats\CachedStatsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class YearlyEstimatedRevenueStats
{
    public function __construct(
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private CachedStatsRepositoryInterface $cachedStatsRepository,
        private CostEstimator $costEstimator,
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
            if ($subscription->getPrice()->getUsage()) {
                $moneyAmount = $this->costEstimator->getEstimate($subscription)->cost;
            } else {
                $moneyAmount = $subscription->getMoneyAmount();
            }

            $originalFee = match ($subscription->getPaymentSchedule()) {
                'week' => $moneyAmount->multipliedBy(52, RoundingMode::HALF_DOWN),
                'month' => $moneyAmount->multipliedBy(12, RoundingMode::HALF_DOWN),
                default => $moneyAmount,
            };

            $amountToAdd = $currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        $stat = $this->cachedStatsRepository->getMoneyStat(CachedStats::STAT_NAME_ESTIMATED_ARR);
        $stat->setValue($money->getMinorAmount()->toInt());
        $this->cachedStatsRepository->save($stat);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Stats\CachedStats;
use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Pricing\Usage\CostEstimator;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\CachedStatsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class MonthlyRevenueStats
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
            if ('year' === $subscription->getPaymentSchedule()) {
                continue;
            }
            if ($subscription->getPrice()->getUsage()) {
                $estimate = $this->costEstimator->getEstimate($subscription);
                $originalFee = $estimate->cost;
            } else {
                $originalFee = $subscription->getMoneyAmount();
                if ('week' === $subscription->getPaymentSchedule()) {
                    $originalFee = $subscription->getMoneyAmount()->multipliedBy(4, RoundingMode::HALF_DOWN);
                }
            }

            $amountToAdd = $currencyConverter->convert($originalFee, $defaultCurrency, RoundingMode::HALF_DOWN);
            $money = $money->plus($amountToAdd, RoundingMode::HALF_DOWN);
        }

        $stat = $this->cachedStatsRepository->getMoneyStat(CachedStats::STAT_NAME_ESTIMATED_MRR);
        $stat->setValue($money->getMinorAmount()->toInt());
        $this->cachedStatsRepository->save($stat);
    }
}

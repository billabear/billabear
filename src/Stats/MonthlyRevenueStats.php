<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Stats;

use App\Payment\ExchangeRates\BricksExchangeRateProvider;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\Stats\MonthlyRevenueStatsRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;

class MonthlyRevenueStats
{
    public function __construct(
        private BricksExchangeRateProvider $exchangeRateProvider,
        private SettingsRepositoryInterface $settingsRepository,
        private MonthlyRevenueStatsRepositoryInterface $monthlyRevenueStatsRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function addSubscription(): void
    {
        $subscriptions = $this->subscriptionRepository->getAll();

        $defaultCurrency = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency();
        $currencyConverter = new CurrencyConverter($this->exchangeRateProvider);
        $money = Money::zero($defaultCurrency);

        foreach ($subscriptions as $subscription) {
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
    }
}

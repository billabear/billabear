<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Stats;

use App\Entity\Customer;
use App\Repository\Stats\ChargeBackAmountDailyStatsRepositoryInterface;
use App\Repository\Stats\ChargeBackAmountMonthlyStatsRepositoryInterface;
use App\Repository\Stats\ChargeBackAmountYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\ChargeBack;

class ChargeBackAmountStats
{
    public function __construct(
        private ChargeBackAmountDailyStatsRepositoryInterface $dailyStatsRepository,
        private ChargeBackAmountMonthlyStatsRepositoryInterface $monthlyStatsRepository,
        private ChargeBackAmountYearlyStatsRepositoryInterface $yearlyStatsRepository,
    ) {
    }

    public function process(ChargeBack $chargeBack): void
    {
        $payment = $chargeBack->getPayment();
        /** @var Customer $customer */
        $customer = $payment->getCustomer();
        $brand = $customer?->getBrand() ?? Customer::DEFAULT_BRAND;

        $dailyStat = $this->dailyStatsRepository->getStatForDateTimeAndCurrency($payment->getCreatedAt(), $payment->getMoneyAmount()->getCurrency(), $brand);
        $dailyStat->increaseAmount($payment->getMoneyAmount());
        $this->dailyStatsRepository->save($dailyStat);

        $monthlyStat = $this->monthlyStatsRepository->getStatForDateTimeAndCurrency($payment->getCreatedAt(), $payment->getMoneyAmount()->getCurrency(), $brand);
        $monthlyStat->increaseAmount($payment->getMoneyAmount());
        $this->dailyStatsRepository->save($monthlyStat);

        $yearlyStat = $this->yearlyStatsRepository->getStatForDateTimeAndCurrency($payment->getCreatedAt(), $payment->getMoneyAmount()->getCurrency(), $brand);
        $yearlyStat->increaseAmount($payment->getMoneyAmount());
        $this->yearlyStatsRepository->save($yearlyStat);
    }
}

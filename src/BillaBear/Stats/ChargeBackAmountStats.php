<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\ChargeBackAmountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\ChargeBackAmountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\ChargeBackAmountYearlyStatsRepositoryInterface;
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

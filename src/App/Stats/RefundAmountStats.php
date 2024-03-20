<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Stats;

use App\Entity\Customer;
use App\Repository\Stats\RefundAmountDailyStatsRepositoryInterface;
use App\Repository\Stats\RefundAmountMonthlyStatsRepositoryInterface;
use App\Repository\Stats\RefundAmountYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Refund;

class RefundAmountStats
{
    public function __construct(
        private RefundAmountDailyStatsRepositoryInterface $dailyStatsRepository,
        private RefundAmountMonthlyStatsRepositoryInterface $monthlyStatsRepository,
        private RefundAmountYearlyStatsRepositoryInterface $yearlyStatsRepository,
    ) {
    }

    public function process(Refund $refund): void
    {
        $payment = $refund->getPayment();
        /** @var Customer $customer */
        $customer = $payment->getCustomer();
        $brand = $customer?->getBrand() ?? Customer::DEFAULT_BRAND;

        $dailyStat = $this->dailyStatsRepository->getStatForDateTimeAndCurrency($refund->getCreatedAt(), $payment->getMoneyAmount()->getCurrency(), $brand);
        $dailyStat->increaseAmount($refund->getMoneyAmount());
        $this->dailyStatsRepository->save($dailyStat);

        $monthlyStat = $this->monthlyStatsRepository->getStatForDateTimeAndCurrency($refund->getCreatedAt(), $payment->getMoneyAmount()->getCurrency(), $brand);
        $monthlyStat->increaseAmount($refund->getMoneyAmount());
        $this->dailyStatsRepository->save($monthlyStat);

        $yearlyStat = $this->yearlyStatsRepository->getStatForDateTimeAndCurrency($refund->getCreatedAt(), $payment->getMoneyAmount()->getCurrency(), $brand);
        $yearlyStat->increaseAmount($refund->getMoneyAmount());
        $this->yearlyStatsRepository->save($yearlyStat);
    }
}

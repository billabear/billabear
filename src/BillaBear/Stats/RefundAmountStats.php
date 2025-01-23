<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\Aggregate\RefundAmountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\RefundAmountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\RefundAmountYearlyStatsRepositoryInterface;
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

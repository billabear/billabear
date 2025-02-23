<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\Aggregate\PaymentAmountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\PaymentAmountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\PaymentAmountYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Payment;

class PaymentAmountStats
{
    public function __construct(
        private PaymentAmountDailyStatsRepositoryInterface $dailyStatsRepository,
        private PaymentAmountMonthlyStatsRepositoryInterface $monthlyStatsRepository,
        private PaymentAmountYearlyStatsRepositoryInterface $yearlyStatsRepository,
    ) {
    }

    public function process(Payment $payment): void
    {
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

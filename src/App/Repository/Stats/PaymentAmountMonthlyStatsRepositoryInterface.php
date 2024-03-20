<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Repository\Stats;

use App\Entity\Stats\PaymentAmountMonthlyStats;
use Brick\Money\Currency;

interface PaymentAmountMonthlyStatsRepositoryInterface extends AmountRepositoryInterface
{
    public function getStatForDateTimeAndCurrency(\DateTimeInterface $dateTime, Currency $currency, string $brandCode): PaymentAmountMonthlyStats;
}

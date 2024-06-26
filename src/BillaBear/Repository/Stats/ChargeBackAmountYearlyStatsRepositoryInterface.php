<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats;

use BillaBear\Entity\Stats\ChargeBackAmountYearlyStats;
use Brick\Money\Currency;

interface ChargeBackAmountYearlyStatsRepositoryInterface extends AmountRepositoryInterface
{
    public function getStatForDateTimeAndCurrency(\DateTimeInterface $dateTime, Currency $currency, string $brandCode): ChargeBackAmountYearlyStats;
}

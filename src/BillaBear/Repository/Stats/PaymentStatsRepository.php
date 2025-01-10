<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats;

use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PaymentStatsRepository implements PaymentStatsRepositoryInterface
{
    public function __construct(
        #[Autowire('@doctrine.dbal.default_connection')]
        private Connection $connection,
    ) {
    }

    public function getDailyPaymentStatesForAMonth(): array
    {
        // TODO: Implement getDailyPaymentStatesForAMonth() method.
    }

    public function getMonthlyPaymentStatsForAYear(): array
    {
    }

    public function getYearlyPaymentStats(): array
    {
        // TODO: Implement getYearlyPaymentStats() method.
    }
}

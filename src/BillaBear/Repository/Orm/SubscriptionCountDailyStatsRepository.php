<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Orm;

use BillaBear\Entity\Stats\SubscriptionCountDailyStats;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\Common\Repository\CustomServiceRepository;

class SubscriptionCountDailyStatsRepository extends CustomServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionCountDailyStats::class);
    }
}

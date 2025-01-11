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
        $result = $this->connection->executeQuery('select count(*), sum(p.converted_amount) as amount, p.converted_currency as currency, sp."name",  
EXTRACT(MONTH FROM p.created_at) as "month", EXTRACT(year FROM p.created_at) as "year" 
from payment p
inner join payment_subscription ps ON ps.payment_id =p.id 
inner join "subscription" s on s.id = ps.subscription_id 
inner join subscription_plan sp on sp.id = s.subscription_plan_id 
WHERE p.created_at >= NOW() - INTERVAL \'12 months\'
group by sp."name", p.converted_currency, EXTRACT(MONTH FROM p.created_at), EXTRACT(year FROM p.created_at)
order by EXTRACT(year FROM p.created_at), EXTRACT(MONTH FROM p.created_at);');

        return $result->fetchAllAssociative();
    }

    public function getYearlyPaymentStats(): array
    {
        // TODO: Implement getYearlyPaymentStats() method.
    }
}

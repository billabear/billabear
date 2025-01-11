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
        $result = $this->connection->executeQuery('select count(*), sum(rl.converted_total) as amount,  sp."name",  
EXTRACT(DAY FROM p.created_at) as "day", EXTRACT(MONTH FROM p.created_at) as "month", EXTRACT(year FROM p.created_at) as "year" 
from receipt_line rl
inner join receipt r on r.id = rl.receipt_id 
inner join payment p on p.id = r.payment_id    
inner join "subscription" s on s.id = rl.subscription_id 
inner join subscription_plan sp on sp.id = s.subscription_plan_id 
WHERE p.created_at >= NOW() - INTERVAL \'30 days\'
group by sp."name", EXTRACT(DAY FROM p.created_at), EXTRACT(MONTH FROM p.created_at), EXTRACT(year FROM p.created_at)
order by EXTRACT(year FROM p.created_at), EXTRACT(MONTH FROM p.created_at), EXTRACT(DAY FROM p.created_at)');

        return $result->fetchAllAssociative();
    }

    public function getMonthlyPaymentStatsForAYear(): array
    {
        $result = $this->connection->executeQuery('select count(*), sum(rl.converted_total) as amount,  sp."name",  
EXTRACT(MONTH FROM p.created_at) as "month", EXTRACT(year FROM p.created_at) as "year" 
from receipt_line rl
inner join receipt r on r.id = rl.receipt_id 
inner join payment p on p.id = r.payment_id
inner join "subscription" s on s.id = rl.subscription_id 
inner join subscription_plan sp on sp.id = s.subscription_plan_id 
WHERE p.created_at >= NOW() - INTERVAL \'12 months\'
group by sp."name", EXTRACT(MONTH FROM p.created_at), EXTRACT(year FROM p.created_at)
order by EXTRACT(year FROM p.created_at), EXTRACT(MONTH FROM p.created_at);');

        return $result->fetchAllAssociative();
    }

    public function getYearlyPaymentStats(): array
    {
        $result = $this->connection->executeQuery('select count(*),sum(rl.converted_total) as amount, sp."name",  
EXTRACT(year FROM p.created_at) as "year" 
from receipt_line rl
inner join receipt r on r.id = rl.receipt_id 
inner join payment p on p.id = r.payment_id
inner join "subscription" s on s.id = rl.subscription_id 
inner join subscription_plan sp on sp.id = s.subscription_plan_id 
WHERE p.created_at >= NOW() - INTERVAL \'5 years\'
group by sp."name", EXTRACT(year FROM p.created_at)
order by EXTRACT(year FROM p.created_at)');

        return $result->fetchAllAssociative();
    }
}

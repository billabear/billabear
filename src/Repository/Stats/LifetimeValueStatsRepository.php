<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository\Stats;

use Parthenon\Common\Repository\DoctrineRepository;

class LifetimeValueStatsRepository extends DoctrineRepository implements LifetimeValueStatsRepositoryInterface
{
    public function getAverageLifespan(array $filters = []): float
    {
        $conn = $this->entityRepository->getEntityManager()->getConnection();

        $sql = 'SELECT AVG(EXTRACT(EPOCH FROM (COALESCE(s.ended_at , NOW()) - s.started_at))) AS avg_duration FROM "subscription" s INNER JOIN "customers" c ON c.id = s.customer_id';
        $sql .= $this->buildCondition($filters);

        $stmt = $conn->prepare($sql);
        $res = $stmt->executeQuery($filters);
        $row = $res->fetchAssociative();
        if (isset($row['avg_duration'])) {
            $lifespan = $row['avg_duration'] / 60 / 60 / 24 / 365;
        } else {
            $lifespan = 0;
        }

        return round($lifespan, 2);
    }

    public function getPaymentTotals(array $filters = []): array
    {
        $conn = $this->entityRepository->getEntityManager()->getConnection();

        $sql = 'select sum(s.amount) as amount, s.currency, s.payment_schedule  from "subscription" s INNER JOIN "customers" c ON c.id = s.customer_id';
        $sql .= $this->buildCondition($filters);
        $sql .= ' group by s.currency, s.payment_schedule';

        $stmt = $conn->prepare($sql);
        $res = $stmt->executeQuery($filters);

        return $res->fetchAllAssociative();
    }

    public function getUniqueCustomerCount(array $filters = []): int
    {
        $conn = $this->entityRepository->getEntityManager()->getConnection();
        $sql = 'select count(distinct s.customer_id) as customer_count  from "subscription" s INNER JOIN "customers" c ON c.id = s.customer_id';
        $sql .= $this->buildCondition($filters);

        $stmt = $conn->prepare($sql);
        $res = $stmt->executeQuery($filters);

        return $res->fetchAssociative()['customer_count'] ?? 0;
    }

    private function buildCondition(array $filters): string
    {
        $output = '';
        $parts = [];

        if (isset($filters['country'])) {
            $parts[] = 'c.billing_address_country = :country';
        }

        if (isset($filters['payment_schedule'])) {
            $parts[] = 's.payment_schedule = :payment_schedule';
        }

        if (isset($filters['subscription_plan'])) {
            $parts[] = 's.subscription_plan_id = :subscription_plan';
        }

        if (isset($filters['brand'])) {
            $parts[] = 'c.brand_settings_id = :brand';
        }

        if (0 === count($parts)) {
            return $output;
        }

        return ' WHERE '.implode(' AND ', $parts);
    }
}

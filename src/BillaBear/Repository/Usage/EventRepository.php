<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Usage;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\Usage\Event;
use BillaBear\Entity\Usage\Metric;
use BillaBear\Enum\MetricFilterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EventRepository implements EventRepositoryInterface
{
    public function __construct(
        #[Autowire('@doctrine.dbal.default_connection')]
        private Connection $connection)
    {
    }

    public function save(Event $entity)
    {
        $query = $this->connection->prepare('INSERT INTO event (id, created_at, customer_id, subscription_id, metric_id, event_id, value, properties) 
VALUES (:id, :createdAt, :customerId, :subscriptionId, :metricId, :eventId, :value, :properties)');
        $query->bindValue('id', (string) $entity->getId());
        $query->bindValue('createdAt', (string) $entity->getCreatedAt()->format(\DateTime::ATOM));
        $query->bindValue('customerId', (string) $entity->getCustomer()->getId());
        $query->bindValue('subscriptionId', (string) $entity->getSubscription()->getId());
        $query->bindValue('metricId', (string) $entity->getMetric()->getId());
        $query->bindValue('eventId', (string) $entity->getEventId());
        $query->bindValue('value', (float) $entity->getValue());
        $query->bindValue('properties', json_encode($entity->getProperties()));
        $query->execute();
    }

    public function getCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountSql($customer, $metric, $subscription, 'month');

        return (float) $result->fetchAssociative()['count_val'];
    }

    public function getCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountSql($customer, $metric, $subscription, 'week');

        return (float) $result->fetchAssociative()['count_val'];
    }

    public function getCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountSql($customer, $metric, $subscription, 'year');

        return (float) $result->fetchAssociative()['count_val'];
    }

    public function getSumForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createSumSql($customer, $metric, $subscription, 'month');

        return (float) $result->fetchAssociative()['sum_val'];
    }

    public function getSumForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createSumSql($customer, $metric, $subscription, 'week');

        return (float) $result->fetchAssociative()['sum_val'];
    }

    public function getSumForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createSumSql($customer, $metric, $subscription, 'year');

        return (float) $result->fetchAssociative()['sum_val'];
    }

    public function getMaxForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createMaxSql($customer, $metric, $subscription, 'month');

        return (float) $result->fetchAssociative()['max_val'];
    }

    public function getMaxForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createMaxSql($customer, $metric, $subscription, 'week');

        return (float) $result->fetchAssociative()['max_val'];
    }

    public function getMaxForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createMaxSql($customer, $metric, $subscription, 'year');

        return (float) $result->fetchAssociative()['max_val'];
    }

    public function getLatestForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createLatestSql($customer, $metric, $subscription, 'month');

        return (float) $result->fetchAssociative()['latest_val'];
    }

    public function getLatestForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createLatestSql($customer, $metric, $subscription, 'week');

        return (float) $result->fetchAssociative()['latest_val'];
    }

    public function getLatestForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createLatestSql($customer, $metric, $subscription, 'year');

        return (float) $result->fetchAssociative()['latest_val'];
    }

    public function getUniqueCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountUnique($customer, $metric, $subscription, 'month');

        return (float) $result->fetchAssociative()['count_val'];
    }

    public function getUniqueCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountUnique($customer, $metric, $subscription, 'week');

        return (float) $result->fetchAssociative()['count_val'];
    }

    public function getUniqueCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountUnique($customer, $metric, $subscription, 'year');

        return (float) $result->fetchAssociative()['count_val'];
    }

    private function createCountSql(Customer $customer, Metric $metric, Subscription $subscription, string $time): Result
    {
        $sql = 'SELECT COUNT(DISTINCT event_id) as count_val'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL;

        $filters = $metric->getFilters();
        $counter = 1;
        foreach ($filters as $filter) {
            if (MetricFilterType::EXCLUSIVE === $filter->getType()) {
                $sql .= sprintf("AND (properties->>'%s' IS NULL OR properties->>'%s' != :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            } else {
                $sql .= sprintf("AND (properties->>'%s' IS NOT NULL AND properties->>'%s' = :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            }
            ++$counter;
        }

        $sql .= match ($time) {
            'week' => 'AND created_at > NOW() - INTERVAL \'1 week\'',
            'year' => 'AND created_at > NOW() - INTERVAL \'1 year\'',
            'day' => 'AND created_at > NOW() - INTERVAL \'1 day\'',
            default => 'AND created_at > NOW() - INTERVAL \'1 month\'',
        };

        $query = $this->connection->prepare($sql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
        }

        return $query->execute();
    }

    private function createSumSql(Customer $customer, Metric $metric, Subscription $subscription, string $time): Result
    {
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id, value'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL;

        $filters = $metric->getFilters();
        $counter = 1;
        foreach ($filters as $filter) {
            if (MetricFilterType::EXCLUSIVE === $filter->getType()) {
                $sql .= sprintf("AND (properties->>'%s' IS NULL OR properties->>'%s' != :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            } else {
                $sql .= sprintf("AND (properties->>'%s' IS NOT NULL AND properties->>'%s' = :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            }
            ++$counter;
        }

        $sql .= match ($time) {
            'week' => 'AND created_at > NOW() - INTERVAL \'1 week\'',
            'year' => 'AND created_at > NOW() - INTERVAL \'1 year\'',
            'day' => 'AND created_at > NOW() - INTERVAL \'1 day\'',
            default => 'AND created_at > NOW() - INTERVAL \'1 month\'',
        };

        $finalSql = "SELECT customer_id, SUM(value) as sum_val FROM ($sql) AS unique_events GROUP BY customer_id";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
        }

        return $query->execute();
    }

    private function createMaxSql(Customer $customer, Metric $metric, Subscription $subscription, string $time): Result
    {
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id, properties->>\'%s\'as value'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL;
        $sql = sprintf($sql, $metric->getAggregationProperty());

        $filters = $metric->getFilters();
        $counter = 1;
        foreach ($filters as $filter) {
            if (MetricFilterType::EXCLUSIVE === $filter->getType()) {
                $sql .= sprintf("AND (properties->>'%s' IS NULL OR properties->>'%s' != :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            } else {
                $sql .= sprintf("AND (properties->>'%s' IS NOT NULL AND properties->>'%s' = :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            }
            ++$counter;
        }

        $sql .= match ($time) {
            'week' => 'AND created_at > NOW() - INTERVAL \'1 week\'',
            'year' => 'AND created_at > NOW() - INTERVAL \'1 year\'',
            'day' => 'AND created_at > NOW() - INTERVAL \'1 day\'',
            default => 'AND created_at > NOW() - INTERVAL \'1 month\'',
        };

        $finalSql = "SELECT customer_id, MAX(value) as max_val FROM ($sql) AS unique_events GROUP BY customer_id";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
        }

        return $query->execute();
    }

    private function createLatestSql(Customer $customer, Metric $metric, Subscription $subscription, string $time): Result
    {
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id, properties->>\'%s\'as value, created_at'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL;
        $sql = sprintf($sql, $metric->getAggregationProperty());

        $filters = $metric->getFilters();
        $counter = 1;
        foreach ($filters as $filter) {
            if (MetricFilterType::EXCLUSIVE === $filter->getType()) {
                $sql .= sprintf("AND (properties->>'%s' IS NULL OR properties->>'%s' != :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            } else {
                $sql .= sprintf("AND (properties->>'%s' IS NOT NULL AND properties->>'%s' = :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            }
            ++$counter;
        }

        $sql .= match ($time) {
            'week' => 'AND created_at > NOW() - INTERVAL \'1 week\'',
            'year' => 'AND created_at > NOW() - INTERVAL \'1 year\'',
            'day' => 'AND created_at > NOW() - INTERVAL \'1 day\'',
            default => 'AND created_at > NOW() - INTERVAL \'1 month\'',
        };

        $finalSql = "SELECT value as latest_val FROM ($sql) AS unique_events order by created_at desc limit 1";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
        }

        return $query->execute();
    }

    private function createCountUnique(Customer $customer, Metric $metric, Subscription $subscription, string $time): Result
    {
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id,  properties->>\'%s\'as value'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL;
        $sql = sprintf($sql, $metric->getAggregationProperty());

        $filters = $metric->getFilters();
        $counter = 1;
        foreach ($filters as $filter) {
            if (MetricFilterType::EXCLUSIVE === $filter->getType()) {
                $sql .= sprintf("AND (properties->>'%s' IS NULL OR properties->>'%s' != :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            } else {
                $sql .= sprintf("AND (properties->>'%s' IS NOT NULL AND properties->>'%s' = :filter%d)", $filter->getName(), $filter->getName(), $counter).PHP_EOL;
            }
            ++$counter;
        }

        $sql .= match ($time) {
            'week' => 'AND created_at > NOW() - INTERVAL \'1 week\'',
            'year' => 'AND created_at > NOW() - INTERVAL \'1 year\'',
            'day' => 'AND created_at > NOW() - INTERVAL \'1 day\'',
            default => 'AND created_at > NOW() - INTERVAL \'1 month\'',
        };

        $finalSql = "SELECT customer_id, COUNT(DISTINCT value) as count_val FROM ($sql) AS unique_events GROUP BY customer_id";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
        }

        return $query->execute();
    }

    public function getEventCountAfterDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): int
    {
        $sql = 'SELECT COUNT(event_id) as count_val'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL
            .'AND created_at > :dateTime'.PHP_EOL;

        $query = $this->connection->prepare($sql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());
        $query->bindValue('dateTime', $dateTime);

        return $query->execute()->fetchAssociative()['count_val'];
    }
}

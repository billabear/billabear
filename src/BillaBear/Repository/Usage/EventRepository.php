<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Usage;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\Usage\Event;
use BillaBear\Entity\Usage\Metric;
use BillaBear\Pricing\Usage\MetricFilterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EventRepository implements EventRepositoryInterface
{
    use LoggerAwareTrait;

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
        $query->bindValue('createdAt', (string) $entity->getCreatedAt()->format(\DateTimeInterface::ATOM));
        $query->bindValue('customerId', (string) $entity->getCustomer()->getId());
        $query->bindValue('subscriptionId', (string) $entity->getSubscription()->getId());
        $query->bindValue('metricId', (string) $entity->getMetric()->getId());
        $query->bindValue('eventId', (string) $entity->getEventId());
        $query->bindValue('value', (float) $entity->getValue());
        $query->bindValue('properties', json_encode($entity->getProperties()));
        $query->executeQuery();
    }

    public function getUniqueCustomerIdsSince(\DateTime $dateTime): array
    {
        $query = $this->connection->prepare('SELECT DISTINCT customer_id FROM event WHERE created_at > TO_TIMESTAMP(:dateTime)');
        $query->bindValue('dateTime', $dateTime->getTimestamp());
        $result = $query->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getCountForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float
    {
        $result = $this->createCountSql($customer, $metric, $subscription, $dateTime);

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountSql($customer, $metric, $subscription, new \DateTime('-1 month'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountSql($customer, $metric, $subscription, new \DateTime('-1 week'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountSql($customer, $metric, $subscription, new \DateTime('-1 year'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getSumForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float
    {
        $result = $this->createSumSql($customer, $metric, $subscription, $dateTime);

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['sum_val'];
    }

    public function getSumForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createSumSql($customer, $metric, $subscription, new \DateTime('-1 month'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['sum_val'];
    }

    public function getSumForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createSumSql($customer, $metric, $subscription, new \DateTime('-1 week'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['sum_val'];
    }

    public function getSumForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createSumSql($customer, $metric, $subscription, new \DateTime('-1 year'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['sum_val'];
    }

    public function getMaxForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float
    {
        $result = $this->createMaxSql($customer, $metric, $subscription, $dateTime);

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['max_val'];
    }

    public function getMaxForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createMaxSql($customer, $metric, $subscription, new \DateTime('-1 month'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['max_val'];
    }

    public function getMaxForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createMaxSql($customer, $metric, $subscription, new \DateTime('-1 week'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['max_val'];
    }

    public function getMaxForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createMaxSql($customer, $metric, $subscription, new \DateTime('-1 year'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['max_val'];
    }

    public function getLatestForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float
    {
        $result = $this->createLatestSql($customer, $metric, $subscription, $dateTime);

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['latest_val'];
    }

    public function getLatestForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createLatestSql($customer, $metric, $subscription, new \DateTime('-1 month'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['latest_val'];
    }

    public function getLatestForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createLatestSql($customer, $metric, $subscription, new \DateTime('-1 week'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['latest_val'];
    }

    public function getLatestForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createLatestSql($customer, $metric, $subscription, new \DateTime('-1 year'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['latest_val'];
    }

    public function getUniqueCountForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float
    {
        $result = $this->createCountUnique($customer, $metric, $subscription, $dateTime);

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getUniqueCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountUnique($customer, $metric, $subscription, new \DateTime('-1 month'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getUniqueCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountUnique($customer, $metric, $subscription, new \DateTime('-1 week'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getUniqueCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float
    {
        $result = $this->createCountUnique($customer, $metric, $subscription, new \DateTime('-1 year'));

        $array = $result->fetchAssociative();

        if (!$array) {
            return 0.0;
        }

        return $array['count_val'];
    }

    public function getEventCountAfterDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): int
    {
        $this->setTimezone();
        $sql = 'SELECT COUNT(event_id) as count_val'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL
            .'AND created_at > TO_TIMESTAMP(:dateTime)'.PHP_EOL;

        $query = $this->connection->prepare($sql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());
        $query->bindValue('dateTime', $dateTime->getTimestamp());

        return $query->execute()->fetchAssociative()['count_val'];
    }

    private function createCountSql(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): Result
    {
        $this->setTimezone();
        $sql = 'SELECT COUNT(DISTINCT event_id) as count_val'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL
            .'AND created_at > TO_TIMESTAMP(:dateTime)'.PHP_EOL;

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

        $query = $this->connection->prepare($sql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());
        $query->bindValue('dateTime', $dateTime->getTimestamp());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
        }

        return $query->execute();
    }

    private function createSumSql(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): Result
    {
        $this->setTimezone();
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id, value'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL
            .'AND created_at > TO_TIMESTAMP(:dateTime)'.PHP_EOL;

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

        $finalSql = "SELECT customer_id, SUM(value) as sum_val FROM ($sql) AS unique_events GROUP BY customer_id";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());
        $query->bindValue('dateTime', $dateTime->getTimestamp());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf('filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
            ++$counter;
        }

        return $query->execute();
    }

    private function createMaxSql(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): Result
    {
        $this->setTimezone();
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id, properties->>\'%s\'as value'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL
            .'AND created_at > TO_TIMESTAMP(:dateTime)'.PHP_EOL;

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

        $finalSql = "SELECT customer_id, MAX(value) as max_val FROM ($sql) AS unique_events GROUP BY customer_id";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());
        $query->bindValue('dateTime', $dateTime->getTimestamp());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
            ++$counter;
        }

        return $query->execute();
    }

    private function createLatestSql(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): Result
    {
        $this->setTimezone();
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id, properties->>\'%s\'as value, created_at'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL
            .'AND created_at > TO_TIMESTAMP(:dateTime)'.PHP_EOL;
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

        $finalSql = "SELECT value as latest_val FROM ($sql) AS unique_events order by created_at desc limit 1";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());
        $query->bindValue('dateTime', $dateTime->getTimestamp());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
            ++$counter;
        }

        return $query->execute();
    }

    private function createCountUnique(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): Result
    {
        $this->setTimezone();
        $sql = 'SELECT DISTINCT ON (event_id) customer_id, event_id,  properties->>\'%s\'as value'.PHP_EOL
            .'FROM event'.PHP_EOL
            .'WHERE customer_id = :customerId'.PHP_EOL
            .'AND metric_id = :metricId'.PHP_EOL
            .'AND subscription_id = :subscriptionId'.PHP_EOL
            .'AND created_at > TO_TIMESTAMP(:dateTime)'.PHP_EOL;
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

        $finalSql = "SELECT customer_id, COUNT(DISTINCT value) as count_val FROM ($sql) AS unique_events GROUP BY customer_id";

        $query = $this->connection->prepare($finalSql);
        $query->bindValue('customerId', (string) $customer->getId());
        $query->bindValue('subscriptionId', (string) $subscription->getId());
        $query->bindValue('metricId', (string) $metric->getId());
        $query->bindValue('dateTime', $dateTime->getTimestamp());

        $counter = 1;
        foreach ($filters as $filter) {
            $varName = sprintf(':filter%d', $counter);
            $query->bindValue($varName, $filter->getValue());
            ++$counter;
        }

        return $query->execute();
    }

    private function setTimezone()
    {
        $this->connection->exec('set time zone UTC;');
    }
}

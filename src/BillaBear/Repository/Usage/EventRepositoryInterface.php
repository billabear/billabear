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

interface EventRepositoryInterface
{
    public function save(Event $entity);

    public function getUniqueCustomerIdsSince(\DateTime $dateTime): array;

    public function getEventCountAfterDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): int;

    public function getCountForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float;

    public function getCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getSumForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float;

    public function getSumForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getSumForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getSumForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getMaxForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float;

    public function getMaxForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getMaxForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getMaxForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getLatestForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float;

    public function getLatestForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getLatestForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getLatestForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getUniqueCountForDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): float;

    public function getUniqueCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getUniqueCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getUniqueCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float;
}

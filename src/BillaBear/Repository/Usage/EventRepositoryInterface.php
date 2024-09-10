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

interface EventRepositoryInterface
{
    public function save(Event $entity);

    public function getEventCountAfterDateTime(Customer $customer, Metric $metric, Subscription $subscription, \DateTime $dateTime): int;

    public function getCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getSumForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getSumForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getSumForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getMaxForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getMaxForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getMaxForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getLatestForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getLatestForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getLatestForYear(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getUniqueCountForMonth(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getUniqueCountForWeek(Customer $customer, Metric $metric, Subscription $subscription): float;

    public function getUniqueCountForYear(Customer $customer, Metric $metric, Subscription $subscription): float;
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Usage;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Usage\Metric;
use BillaBear\Entity\Usage\MetricCounter;
use Parthenon\Athena\Repository\DoctrineCrudRepository;

class MetricCounterRepository extends DoctrineCrudRepository implements MetricCounterRepositoryInterface
{
    public function getMetricUsageForCustomerAndMetric(Customer $customer, Metric $metric): MetricCounter
    {
        $usage = $this->entityRepository->findOneBy(['customer' => $customer, 'metric' => $metric]);

        if (!$usage) {
            $usage = new MetricCounter();
            $usage->setCustomer($customer);
            $usage->setMetric($metric);
            $usage->setValue(0);
        }

        return $usage;
    }
}

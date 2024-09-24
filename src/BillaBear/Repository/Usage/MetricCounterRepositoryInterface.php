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
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface MetricCounterRepositoryInterface extends CrudRepositoryInterface
{
    public function getForCustomerAndMetric(Customer $customer, Metric $metric): MetricCounter;

    public function getAllForCustomer(Customer $customer): array;
}

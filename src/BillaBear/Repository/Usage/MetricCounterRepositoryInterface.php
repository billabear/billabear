<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

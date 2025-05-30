<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Usage;

use BillaBear\Entity\Usage\Metric;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface MetricRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return Metric[]
     */
    public function getAll(): array;

    public function getByCode(string $code): Metric;
}

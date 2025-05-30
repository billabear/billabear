<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\CancellationRequest;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface CancellationRequestRepositoryInterface extends CrudRepositoryInterface
{
    public function getDailyCount(\DateTime $dateTime): array;

    public function getMonthlyCount(\DateTime $dateTime): array;

    public function getYearlyCount(\DateTime $dateTime): array;

    /**
     * @return \Generator|CancellationRequest[]
     */
    public function getFailedProcesses(): \Generator;
}

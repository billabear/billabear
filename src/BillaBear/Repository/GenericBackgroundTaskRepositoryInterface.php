<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\GenericBackgroundTask;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface GenericBackgroundTaskRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return GenericBackgroundTask[]
     */
    public function getNonCompleted(): array;
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\MassSubscriptionChange;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

/**
 * @method \App\Entity\MassSubscriptionChange findById($id)
 */
interface MassSubscriptionChangeRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return MassSubscriptionChange[]
     */
    public function findWithinFiveMinutes(\DateTime $dateTime): array;
}

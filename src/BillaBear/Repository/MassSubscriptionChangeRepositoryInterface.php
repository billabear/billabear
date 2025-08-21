<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\MassSubscriptionChange;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

/**
 * @method MassSubscriptionChange findById($id)
 */
interface MassSubscriptionChangeRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return MassSubscriptionChange[]
     */
    public function findWithinFiveMinutes(\DateTime $dateTime): array;
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\InviteCode;

class InviteCodeRepository extends \Parthenon\User\Repository\InviteCodeRepository implements InviteCodeRepositoryInterface
{
    /**
     * @return InviteCode[]
     */
    public function findAllUnusedInvites(): array
    {
        return $this->entityRepository->findBy(['used' => false]);
    }
}

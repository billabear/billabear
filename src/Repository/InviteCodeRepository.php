<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository;

use App\Entity\InviteCode;
use App\Entity\Team;

class InviteCodeRepository extends \Parthenon\User\Repository\InviteCodeRepository implements InviteCodeRepositoryInterface
{
    /**
     * @return InviteCode[]
     */
    public function findAllUnusedInvitesForTeam(Team $team): array
    {
        return $this->entityRepository->findBy(['team' => $team, 'used' => false]);
    }

    public function getUsableInviteCount(Team $team): int
    {
        return $this->entityRepository->count(['team' => $team, 'used' => false, 'cancelled' => false]);
    }
}

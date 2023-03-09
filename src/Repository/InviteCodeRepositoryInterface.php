<?php

namespace App\Repository;

use App\Entity\InviteCode;
use App\Entity\Team;

interface InviteCodeRepositoryInterface extends \Parthenon\User\Repository\InviteCodeRepositoryInterface
{
    /**
     * @return InviteCode[]
     */
    public function findAllUnusedInvitesForTeam(Team $team): array;

    /**
     * @return InviteCode[]
     */
    public function getUsableInviteCount(Team $team): int;
}

<?php

namespace App\Repository\Orm;

use App\Entity\TeamInviteCode;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\Common\Repository\CustomServiceRepository;

class TeamInviteCodeRepository extends CustomServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamInviteCode::class);
    }
}

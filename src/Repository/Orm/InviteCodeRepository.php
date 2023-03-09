<?php

namespace App\Repository\Orm;

use App\Entity\InviteCode;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\Common\Repository\CustomServiceRepository;

class InviteCodeRepository extends CustomServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InviteCode::class);
    }
}

<?php

namespace App\Repository\Orm;

use App\Entity\ForgotPasswordCode;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\Common\Repository\CustomServiceRepository;

class ForgotPasswordCodeRepository extends CustomServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForgotPasswordCode::class);
    }
}

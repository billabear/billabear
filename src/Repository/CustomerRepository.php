<?php

namespace App\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Repository\DoctrineRepository;
use Parthenon\User\Entity\UserInterface;

class CustomerRepository extends DoctrineCrudRepository implements CustomerRepositoryInterface
{
    public function getSubscriptionForUser(UserInterface $user): Subscription
    {
        return new Subscription();
    }
}

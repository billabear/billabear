<?php

namespace App\Repository;

use App\Entity\User;
use Parthenon\Payments\SubscriptionInterface;
use Parthenon\User\Entity\UserInterface;

class TeamRepository extends \Parthenon\User\Repository\TeamRepository implements TeamRepositoryInterface
{
    /**
     * @param User $user
     */
    public function getSubscriptionForUser(UserInterface $user): SubscriptionInterface
    {
        return $user->getTeam()->getSubscription();
    }

    public function findAllSubscriptions(): array
    {
        return $this->entityRepository->createQueryBuilder('t')
            ->where('t.subscription.paymentId is not null')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\Payments\Subscriber\SubscriberInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User extends \Parthenon\User\Entity\User implements MemberInterface, LimitedUserInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", fetch="EAGER", inversedBy="members")
     */
    private Team|SubscriberInterface $team;

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function hasTeam(): bool
    {
        return isset($this->team);
    }

    /**
     * @param Team $team
     */
    public function setTeam(TeamInterface $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getPlanName(): ?string
    {
        return $this->team->getSubscription()->getPlanName();
    }
}

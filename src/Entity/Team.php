<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Subscriber\SubscriberInterface;
use Parthenon\User\Entity\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="teams")
 */
class Team extends \Parthenon\User\Entity\Team implements SubscriberInterface
{
    /**
     * @ORM\Embedded(class="Parthenon\Payments\Entity\Subscription")
     */
    private ?Subscription $subscription;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="team")
     *
     * @var UserInterface[]|Collection
     */
    protected Collection $members;

    public function setSubscription(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function hasActiveSubscription(): bool
    {
        if (!$this->subscription) {
            return false;
        }

        return $this->subscription->isActive();
    }

    public function getIdentifier(): string
    {
        return (string) $this->getName();
    }
}

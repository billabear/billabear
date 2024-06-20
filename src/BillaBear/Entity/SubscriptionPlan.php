<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Athena\Entity\DeletableInterface;

/**
 * @method Product getProduct()
 */
#[ORM\Entity]
#[ORM\Table('subscription_plan')]
class SubscriptionPlan extends \Parthenon\Billing\Entity\SubscriptionPlan implements DeletableInterface
{
    #[ORM\Column(type: 'boolean')]
    protected bool $isDeleted = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $deletedAt = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected ?bool $isTrialStandalone = null;

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function markAsDeleted(): DeletableInterface
    {
        $this->deletedAt = new \DateTime('now');
        $this->isDeleted = true;

        return $this;
    }

    public function unmarkAsDeleted(): DeletableInterface
    {
        $this->deletedAt = null;
        $this->isDeleted = false;

        return $this;
    }

    public function getIsTrialStandalone(): bool
    {
        return true === $this->isTrialStandalone;
    }

    public function setIsTrialStandalone(?bool $isTrialStandalone): void
    {
        $this->isTrialStandalone = $isTrialStandalone;
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity;

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
    protected bool $deleted = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $deletedAt = null;

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function markAsDeleted(): DeletableInterface
    {
        $this->deletedAt = new \DateTime('now');
        $this->deleted = true;

        return $this;
    }

    public function unmarkAsDeleted(): DeletableInterface
    {
        $this->deletedAt = null;
        $this->deleted = false;

        return $this;
    }
}

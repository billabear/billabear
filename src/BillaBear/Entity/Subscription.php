<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @method Price|null       getPrice()
 * @method Customer         getCustomer()
 * @method SubscriptionPlan getSubscriptionPlan()
 */
#[ORM\Entity]
#[ORM\Table('subscription')]
class Subscription extends \Parthenon\Billing\Entity\Subscription
{
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function getMetadata(): array
    {
        if (!is_array($this->metadata)) {
            return [];
        }

        return $this->metadata;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }
}

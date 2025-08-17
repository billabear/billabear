<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Logger\Audit\AuditableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method Price|null       getPrice()
 * @method Customer         getCustomer()
 * @method SubscriptionPlan getSubscriptionPlan()
 */
#[ORM\Entity]
#[ORM\Index(columns: ['startedAt', 'status'], name: 'subscription_started_at_status_idx')]
#[ORM\Table('subscription')]
class Subscription extends \Parthenon\Billing\Entity\Subscription implements AuditableInterface
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

    public function getAuditName(): string
    {
        return 'Subscription';
    }

    public function getAuditLogIdTag(): string
    {
        return 'subscription_id';
    }
}

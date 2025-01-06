<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Customer\CustomerSubscriptionEventType;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Index(name: 'event_date_idx', fields: ['eventType', 'createdAt'])]
#[ORM\Table(name: 'customer_subscription_events')]
class CustomerSubscriptionEvent
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private CustomerInterface $customer;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    private Subscription $subscription;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?BillingAdminInterface $doneBy = null;

    #[ORM\Column(type: 'string', length: 255, enumType: CustomerSubscriptionEventType::class)]
    private CustomerSubscriptionEventType $eventType;

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getDoneBy(): ?BillingAdminInterface
    {
        return $this->doneBy;
    }

    public function setDoneBy(?BillingAdminInterface $doneBy): void
    {
        $this->doneBy = $doneBy;
    }

    public function getEventType(): CustomerSubscriptionEventType
    {
        return $this->eventType;
    }

    public function setEventType(CustomerSubscriptionEventType $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

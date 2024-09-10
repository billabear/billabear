<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Usage;

use BillaBear\Database\Doctrine\HyperTable;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'event')]
#[HyperTable(timeColumn: 'created_at')]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    private Subscription $subscription;

    #[ORM\ManyToOne(targetEntity: Metric::class)]
    private Metric $metric;

    #[ORM\Column(type: 'string')]
    private string $eventId;

    #[ORM\Column(type: 'float')]
    private float $value;

    #[ORM\Column(type: 'json')]
    private array $properties;

    #[ORM\Id]
    #[ORM\Column(name: 'created_at', type: 'datetimetz')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
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

    public function getMetric(): Metric
    {
        return $this->metric;
    }

    public function setMetric(Metric $metric): void
    {
        $this->metric = $metric;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function setEventId(string $eventId): void
    {
        $this->eventId = $eventId;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
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

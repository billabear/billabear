<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\CreateEvent;

use BillaBear\Validator\Constraints\CustomerExists;
use BillaBear\Validator\Constraints\MetricCodeExists;
use BillaBear\Validator\Constraints\SubscriptionExists;
use Symfony\Component\Validator\Constraints as Assert;

class CreateEvent
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    private $eventId;

    #[Assert\NotBlank()]
    #[Assert\Uuid]
    #[CustomerExists]
    private $customer;

    #[Assert\NotBlank()]
    #[Assert\Uuid]
    #[SubscriptionExists]
    private $subscription;

    #[Assert\Type('array')]
    private $properties;

    #[Assert\NotBlank()]
    #[Assert\Type(['integer', 'float'])]
    private $value;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[MetricCodeExists]
    private $code;

    public function getEventId()
    {
        return $this->eventId;
    }

    public function setEventId($eventId): void
    {
        $this->eventId = $eventId;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    public function getSubscription()
    {
        return $this->subscription;
    }

    public function setSubscription($subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperties($properties): void
    {
        $this->properties = $properties;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }
}
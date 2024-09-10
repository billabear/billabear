<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Usage;

use BillaBear\Dto\Request\Api\CreateEvent\CreateEvent;
use BillaBear\Entity\Usage\Event as Entity;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Repository\Usage\MetricRepositoryInterface;
use Ramsey\Uuid\Uuid;

class EventDataMapper
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private MetricRepositoryInterface $metricRepository,
    ) {
    }

    public function createEntity(CreateEvent $event): Entity
    {
        $entity = new Entity();
        $entity->setId(Uuid::uuid4());
        $entity->setEventId($event->getEventId());
        $entity->setCustomer($this->customerRepository->findById($event->getCustomer()));
        $entity->setSubscription($this->subscriptionRepository->findById($event->getSubscription()));
        $entity->setMetric($this->metricRepository->getByCode($event->getCode()));
        $entity->setValue(floatval($event->getValue()));
        $entity->setProperties($event->getProperties() ?? []);
        $entity->setCreatedAt(new \DateTime());

        return $entity;
    }
}

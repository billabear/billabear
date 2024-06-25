<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Subscriptions;

use BillaBear\Dto\Generic\App\CustomerSubscriptionEvent as AppDto;
use BillaBear\Entity\CustomerSubscriptionEvent as Entity;

class CustomerSubscriptionEventDataMapper
{
    public function __construct(private SubscriptionDataMapper $subscriptionDataMapper)
    {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setType($entity->getEventType()->value);
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setSubscription($this->subscriptionDataMapper->createAppDto($entity->getSubscription()));

        return $dto;
    }
}

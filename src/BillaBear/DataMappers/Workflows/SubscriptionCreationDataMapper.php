<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Workflows;

use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\Dto\Generic\App\Workflows\SubscriptionCreation as AppDto;
use BillaBear\Entity\SubscriptionCreation as Entity;

class SubscriptionCreationDataMapper
{
    public function __construct(private SubscriptionDataMapper $subscriptionDataMapper)
    {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setState($entity->getState());
        $dto->setSubscription($this->subscriptionDataMapper->createAppDto($entity->getSubscription()));
        $dto->setHasError($entity->getHasError());
        $dto->setError($entity->getError());

        return $dto;
    }
}

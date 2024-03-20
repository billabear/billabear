<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Workflows;

use App\DataMappers\PaymentDataMapper;
use App\Dto\Generic\App\Workflows\PaymentCreation as AppDto;
use App\Entity\PaymentCreation as Entity;

class PaymentCreationDataMapper
{
    public function __construct(private PaymentDataMapper $paymentDataMapper)
    {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setState($entity->getState());
        $dto->setPayment($this->paymentDataMapper->createAppDto($entity->getPayment()));
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setHasError($entity->getHasError());
        $dto->setError($entity->getError());

        return $dto;
    }
}

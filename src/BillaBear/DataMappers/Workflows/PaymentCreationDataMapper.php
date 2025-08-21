<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Workflows;

use BillaBear\DataMappers\PaymentDataMapper;
use BillaBear\Dto\Generic\App\Workflows\PaymentCreation as AppDto;
use BillaBear\Entity\PaymentCreation as Entity;

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

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

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

use App\DataMappers\CustomerDataMapper;
use App\DataMappers\PaymentAttemptDataMapper;
use App\Dto\Generic\App\Workflows\PaymentFailureProcess as AppDto;
use App\Entity\PaymentFailureProcess as Entity;

class PaymentFailureProcessDataMapper
{
    public function __construct(
        private CustomerDataMapper $customerDataMapper,
        private PaymentAttemptDataMapper $paymentAttemptDataMapper,
    ) {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setState($entity->getState());
        $dto->setResolved($entity->isResolved());
        $dto->setNextAttemptAt($entity->getNextAttemptAt());
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setUpdatedAt($entity->getUpdatedAt());
        $dto->setCustomer($this->customerDataMapper->createAppDto($entity->getCustomer()));
        $dto->setPaymentAttempt($this->paymentAttemptDataMapper->createAppDto($entity->getPaymentAttempt()));

        return $dto;
    }
}

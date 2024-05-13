<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Workflows;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\PaymentAttemptDataMapper;
use BillaBear\Dto\Generic\App\Workflows\PaymentFailureProcess as AppDto;
use BillaBear\Entity\PaymentFailureProcess as Entity;

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

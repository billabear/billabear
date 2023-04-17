<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Factory;

use App\Customer\CustomerFactory;
use App\Dto\Generic\Api\Payment as ApiDto;
use App\Dto\Generic\App\Payment as AppDto;
use Parthenon\Billing\Entity\Payment;

class PaymentFactory
{
    public function __construct(
        private CustomerFactory $customerFactory
    ) {
    }

    public function createAppDto(Payment $payment): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $payment->getId());
        $dto->setStatus($payment->getStatus()->value);
        $dto->setAmount($payment->getAmount());
        $dto->setCurrency($payment->getCurrency());
        $dto->setExternalReference($payment->getPaymentReference());
        $dto->setCreatedAt($payment->getCreatedAt());
        $dto->setCustomer($this->customerFactory->createAppDto($payment->getCustomer()));
        $dto->setPaymentProviderDetailsUrl($payment->getPaymentProviderDetailsUrl());

        return $dto;
    }

    public function createApiDto(Payment $payment): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $payment->getId());
        $dto->setStatus($payment->getStatus()->value);
        $dto->setAmount($payment->getAmount());
        $dto->setCurrency($payment->getCurrency());
        $dto->setExternalReference($payment->getPaymentReference());
        $dto->setCreatedAt($payment->getCreatedAt());
        $dto->setCustomer($this->customerFactory->createAppDto($payment->getCustomer()));

        return $dto;
    }
}

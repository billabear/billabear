<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\Payment as ApiDto;
use App\Dto\Generic\App\Payment as AppDto;
use App\Entity\Customer;
use Parthenon\Billing\Entity\Payment;

class PaymentDataMapper
{
    public function __construct(
        private CustomerDataMapper $customerFactory
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
        $dto->setPaymentProviderDetailsUrl($payment->getPaymentProviderDetailsUrl());

        $customer = $payment->getCustomer();
        if ($customer instanceof Customer) {
            $dto->setCustomer($this->customerFactory->createAppDto($customer));
        }

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

        $customer = $payment->getCustomer();
        if ($customer instanceof Customer) {
            $dto->setCustomer($this->customerFactory->createApiDto($customer));
        }

        return $dto;
    }
}

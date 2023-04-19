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
use App\Dto\Generic\Api\Refund as ApiDto;
use App\Dto\Generic\App\Refund as AppDto;
use Parthenon\Billing\Entity\Refund;

class RefundFactory
{
    public function __construct(
        private PaymentFactory $paymentFactory,
        private CustomerFactory $customerFactory,
        private BillingAdminFactory $billingAdminFactory,
    ) {
    }

    public function createAppDto(Refund $refund): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $refund->getId());
        $dto->setAmount($refund->getAmount());
        $dto->setCurrency($refund->getCurrency());
        $dto->setReason($refund->getReason());
        $dto->setStatus($refund->getStatus()->value);
        $dto->setCreatedAt($refund->getCreatedAt());
        $dto->setPayment($this->paymentFactory->createAppDto($refund->getPayment()));
        $dto->setCustomer($this->customerFactory->createAppDto($refund->getCustomer()));
        $dto->setBillingAdmin($this->billingAdminFactory->createAppDto($refund->getBillingAdmin()));

        return $dto;
    }

    public function createApiDto(Refund $refund): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $refund->getId());
        $dto->setAmount($refund->getAmount());
        $dto->setCurrency($refund->getCurrency());
        $dto->setComment($refund->getReason());
        $dto->setStatus($refund->getStatus()->value);
        $dto->setCreatedAt($refund->getCreatedAt());
        $dto->setPayment($this->paymentFactory->createApiDto($refund->getPayment()));
        $dto->setCustomer($this->customerFactory->createApiDto($refund->getCustomer()));
        $dto->setBillingAdmin($this->billingAdminFactory->createAppDto($refund->getBillingAdmin()));

        return $dto;
    }
}

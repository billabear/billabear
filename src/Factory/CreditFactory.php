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

use App\Dto\Generic\App\Credit as AppDto;
use App\Dto\Request\App\CreditNote\CreateCreditNote;
use App\Entity\Credit as Entity;
use App\Entity\Customer;

class CreditFactory
{
    public function __construct(private CustomerFactory $customerFactory, private BillingAdminFactory $billingAdminFactory)
    {
    }

    public function createEntity(CreateCreditNote $createCreditNote, Customer $customer): Entity
    {
        $entity = new Entity();
        $entity->setAmount((int) $createCreditNote->getAmount());
        $entity->setCurrency($createCreditNote->getCurrency());
        $entity->setReason($createCreditNote->getReason());
        $entity->setCustomer($customer);
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());
        $entity->setUsedAmount(0);
        $entity->setCompletelyUsed(false);

        return $entity;
    }

    public function createAppDto(Entity $creditNote): AppDto
    {
        $dto = new AppDto();
        $dto->setCustomer($this->customerFactory->createAppDto($creditNote->getCustomer()));
        $dto->setBillingAdmin($this->billingAdminFactory->createAppDto($creditNote->getBillingAdmin()));
        $dto->setReason($creditNote->getReason());
        $dto->setAmount($creditNote->getAmount());
        $dto->setCurrency($creditNote->getCurrency());
        $dto->setUsedAmount($creditNote->getUsedAmount());
        $dto->setCreatedAt($creditNote->getCreatedAt());

        return $dto;
    }
}

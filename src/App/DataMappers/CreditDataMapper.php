<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\Credit as AppDto;
use App\Dto\Request\App\CreditAdjustment\CreateCreditAdjustment;
use App\Entity\Credit as Entity;
use App\Entity\Customer;
use Obol\Model\Credit\BalanceOutput;

class CreditDataMapper
{
    public function __construct(private CustomerDataMapper $customerFactory, private BillingAdminDataMapper $billingAdminFactory)
    {
    }

    public function createFromObol(Customer $customer, BalanceOutput $balanceOutput): Entity
    {
        $entity = new Entity();
        $entity->setCustomer($customer);
        $entity->setAmount(abs($balanceOutput->getAmount()));
        $entity->setType($balanceOutput->getAmount() > 0 ? Entity::TYPE_CREDIT : Entity::TYPE_DEBIT);
        $entity->setCurrency(strtoupper($balanceOutput->getCurrency()));
        $entity->setCreatedAt($balanceOutput->getCreatedAt());
        $entity->setReason($balanceOutput->getDescription());
        $entity->setUpdatedAt(new \DateTime());
        $entity->setCreationType(Entity::CREATION_TYPE_AUTOMATED);
        $entity->setUsedAmount(abs($balanceOutput->getAmount()));

        return $entity;
    }

    public function createEntity(CreateCreditAdjustment $createCreditNote, Customer $customer): Entity
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
        $entity->setType($createCreditNote->getType());

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
        $dto->setType($creditNote->getType());

        return $dto;
    }
}

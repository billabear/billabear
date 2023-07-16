<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\Voucher as AppDto;
use App\Dto\Request\App\Voucher\CreateVoucher;
use App\Entity\Voucher as Entity;
use App\Entity\VoucherAmount;
use App\Enum\VoucherEntryType;
use App\Enum\VoucherEvent;
use App\Enum\VoucherType;
use Doctrine\Common\Collections\ArrayCollection;

class VoucherFactory
{
    public function createEntity(CreateVoucher $createVoucher): Entity
    {
        $entity = new Entity();

        $entity->setType(VoucherType::fromName($createVoucher->getType()));
        $entity->setEntryType(VoucherEntryType::fromName($createVoucher->getEntryType()));
        $entity->setName($createVoucher->getName() ?? bin2hex(random_bytes(16)));
        $entity->setCode($createVoucher->getCode());

        if ($createVoucher->getEntryEvent()) {
            $entity->setEntryEvent(VoucherEvent::fromName($createVoucher->getEntryEvent()));
        }

        if (VoucherType::PERCENTAGE === $entity->getType()) {
            $entity->setPercentage($createVoucher->getPercentage());
        } else {
            $collection = new ArrayCollection();
            foreach ($createVoucher->getAmounts() as $amount) {
                $amountEntity = new VoucherAmount();
                $amountEntity->setCurrency($amount->getCurrency());
                $amountEntity->setAmount((int) $amount->getAmount());
                $amountEntity->setVoucher($entity);
                $collection->add($amountEntity);
            }
            $entity->setAmounts($collection);
        }

        $entity->setCreatedAt(new \DateTime('now'));

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setId((string) $entity->getId());
        $appDto->setName($entity->getName());
        $appDto->setType($entity->getType());
        $appDto->setEntryType($entity->getEntryType());
        $appDto->setAutomaticEvent($entity->getEntryEvent());
        $appDto->setPercentage($entity->getPercentage());
        $appDto->setCode($entity->getCode());
        $appDto->setDisabled($entity->isDisabled());

        return $appDto;
    }
}

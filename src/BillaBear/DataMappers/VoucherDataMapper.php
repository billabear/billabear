<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\App\Voucher as AppDto;
use BillaBear\Dto\Request\App\Voucher\CreateVoucher;
use BillaBear\Entity\Voucher as Entity;
use BillaBear\Entity\VoucherAmount;
use BillaBear\Voucher\VoucherEntryType;
use BillaBear\Voucher\VoucherEvent;
use BillaBear\Voucher\VoucherType;
use Doctrine\Common\Collections\ArrayCollection;

class VoucherDataMapper
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

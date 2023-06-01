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

use App\Dto\Generic\App\Voucher as AppDto;
use App\Dto\Request\App\Voucher\CreateVoucher;
use App\Entity\Voucher as Entity;
use App\Enum\VoucherEntryType;
use App\Enum\VoucherEvent;
use App\Enum\VoucherType;

class VoucherFactory
{
    public function createEntity(CreateVoucher $createVoucher): Entity
    {
        $entity = new Entity();

        $entity->setType(VoucherType::fromName($createVoucher->getType()));
        $entity->setEntryType(VoucherEntryType::fromName($createVoucher->getEntryType()));
        $entity->setName($createVoucher->getName() ?? bin2hex(random_bytes(16)));

        if ($createVoucher->getEntryEvent()) {
            $entity->setEntryEvent(VoucherEvent::fromName($createVoucher->getEntryEvent()));
        }

        if (VoucherType::PERCENTAGE === $entity->getType()) {
            $entity->setValue($createVoucher->getValue());
        }

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setName($entity->getName());
        $appDto->setType($entity->getType());
        $appDto->setEntryType($entity->getEntryType());
        $appDto->setAutomaticEvent($entity->getEntryEvent());
        $appDto->setValue($entity->getValue());

        return $appDto;
    }
}

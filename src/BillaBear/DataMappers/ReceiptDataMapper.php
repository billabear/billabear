<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\Api\Receipt as ApiDto;
use BillaBear\Dto\Generic\App\Receipt as AppDto;
use Parthenon\Billing\Entity\Receipt;

class ReceiptDataMapper
{
    public function createApiDto(Receipt $receipt): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $receipt->getId());
        $dto->setValid($receipt->isValid());
        $dto->setCreatedAt($receipt->getCreatedAt());

        return $dto;
    }

    public function createAppDto(Receipt $receipt): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $receipt->getId());
        $dto->setCreatedAt($receipt->getCreatedAt());

        return $dto;
    }
}

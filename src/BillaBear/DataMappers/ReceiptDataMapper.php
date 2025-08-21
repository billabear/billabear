<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\Api\Receipt as ApiDto;
use BillaBear\Dto\Generic\App\Receipt as AppDto;
use Parthenon\Billing\Entity\Receipt;

class ReceiptDataMapper
{
    public function createApiDto(Receipt $receipt): ApiDto
    {
        $dto = new ApiDto(
            (string) $receipt->getId(),
            $receipt->getCreatedAt(),
            $receipt->isValid(),
        );

        return $dto;
    }

    public function createAppDto(Receipt $receipt): AppDto
    {
        $dto = new AppDto(
            (string) $receipt->getId(),
            $receipt->getCreatedAt(),
        );

        return $dto;
    }
}

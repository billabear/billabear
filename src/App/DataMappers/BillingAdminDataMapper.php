<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\BillingAdmin as ApiDto;
use App\Dto\Generic\App\BillingAdmin as AppDto;
use Parthenon\Billing\Entity\BillingAdminInterface;

class BillingAdminDataMapper
{
    public function createAppDto(?BillingAdminInterface $billingAdmin): ?AppDto
    {
        if (!$billingAdmin) {
            return null;
        }

        $dto = new AppDto();
        $dto->setId((string) $billingAdmin->getId());
        $dto->setDisplayName($billingAdmin->getDisplayName());

        return $dto;
    }

    public function createApiDto(?BillingAdminInterface $billingAdmin): ?ApiDto
    {
        if (!$billingAdmin) {
            return null;
        }

        $dto = new ApiDto();
        $dto->setId((string) $billingAdmin->getId());
        $dto->setDisplayName($billingAdmin->getDisplayName());

        return $dto;
    }
}

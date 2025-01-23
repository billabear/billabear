<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\Api\BillingAdmin as ApiDto;
use BillaBear\Dto\Generic\App\BillingAdmin as AppDto;
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

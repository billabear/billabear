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

namespace App\Factory;

use App\Dto\Generic\Api\BillingAdmin as ApiDto;
use App\Dto\Generic\App\BillingAdmin as AppDto;
use Parthenon\Billing\Entity\BillingAdminInterface;

class BillingAdminFactory
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

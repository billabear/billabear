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

namespace App\Install\Steps;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Install\Dto\InstallRequest;
use App\Repository\BrandSettingsRepositoryInterface;

class BrandStep
{
    public function __construct(private BrandSettingsRepositoryInterface $brandSettingsRepository)
    {
    }

    public function install(InstallRequest $request): void
    {
        $brand = new BrandSettings();
        $brand->setCode(Customer::DEFAULT_BRAND);
        $brand->setBrandName($request->getDefaultBrand());
        $brand->setIsDefault(true);
        $brand->setEmailAddress($request->getFromEmail());

        $this->brandSettingsRepository->save($brand);
    }
}
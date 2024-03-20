<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Install\Steps;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Install\Dto\InstallRequest;
use App\Repository\BrandSettingsRepositoryInterface;
use Parthenon\Common\Address;

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

        $address = new Address();
        $address->setCountry($request->getCountry());
        $brand->setAddress($address);

        $brand->getNotificationSettings()->setSubscriptionCancellation(true);
        $brand->getNotificationSettings()->setSubscriptionCreation(true);
        $brand->getNotificationSettings()->setExpiringCardWarning(true);
        $brand->getNotificationSettings()->setExpiringCardDayBefore(true);

        $this->brandSettingsRepository->save($brand);
    }
}

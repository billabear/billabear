<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Steps;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Install\Dto\InstallRequest;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
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

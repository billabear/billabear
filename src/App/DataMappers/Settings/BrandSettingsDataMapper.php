<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Settings;

use App\Dto\Generic\Address as AddressDto;
use App\Dto\Request\App\BrandSettings\CreateBrandSettings;
use App\Dto\Request\App\BrandSettings\EditBrandSettings as EditDto;
use App\Dto\Response\App\BrandSettings\BrandSettings as AppDto;
use App\Entity\BrandSettings;
use Parthenon\Common\Address;

class BrandSettingsDataMapper
{
    public function createEntityFromEditDto(EditDto|CreateBrandSettings $dto, ?BrandSettings $brandSettings = null): BrandSettings
    {
        if ($dto instanceof CreateBrandSettings) {
            $brandSettings = new BrandSettings();
            $brandSettings->setCode($dto->getCode());
            $brandSettings->setIsDefault(false);
        }
        $address = new Address();
        $address->setCompanyName($dto->getAddress()->getCompanyName());
        $address->setStreetLineOne($dto->getAddress()->getStreetLineOne());
        $address->setStreetLineTwo($dto->getAddress()->getStreetLineTwo());
        $address->setCountry($dto->getAddress()->getCountry());
        $address->setCity($dto->getAddress()->getCity());
        $address->setRegion($dto->getAddress()->getRegion());
        $address->setPostcode($dto->getAddress()->getPostcode());

        $brandSettings->setBrandName($dto->getName());
        $brandSettings->setAddress($address);
        $brandSettings->setEmailAddress($dto->getEmailAddress());

        $brandSettings->getNotificationSettings()->setSubscriptionCreation($dto->getNotifications()->getSubscriptionCreation());
        $brandSettings->getNotificationSettings()->setSubscriptionCancellation($dto->getNotifications()->getSubscriptionCancellation());
        $brandSettings->getNotificationSettings()->setExpiringCardWarning($dto->getNotifications()->getExpiringCardWarning());
        $brandSettings->getNotificationSettings()->setExpiringCardDayBefore($dto->getNotifications()->getExpiringCardDayBeforeWarning());
        $brandSettings->getNotificationSettings()->setInvoiceCreated($dto->getNotifications()->getInvoiceCreated());
        $brandSettings->getNotificationSettings()->setQuoteCreated($dto->getNotifications()->getQuoteCreated());
        $brandSettings->getNotificationSettings()->setInvoiceOverdue($dto->getNotifications()->getInvoiceOverdue());

        $brandSettings->setTaxNumber($dto->getTaxNumber());
        $brandSettings->setTaxRate($dto->getTaxRate());
        $brandSettings->setDigitalServicesRate($dto->getDigitalServicesTaxRate());

        return $brandSettings;
    }

    public function createAppDto(?BrandSettings $brandSettings): ?AppDto
    {
        if (null === $brandSettings) {
            return null;
        }

        $address = new AddressDto();
        $address->setCompanyName($brandSettings->getAddress()->getCompanyName());
        $address->setStreetLineOne($brandSettings->getAddress()->getStreetLineOne());
        $address->setStreetLineTwo($brandSettings->getAddress()->getStreetLineTwo());
        $address->setCity($brandSettings->getAddress()->getCity());
        $address->setRegion($brandSettings->getAddress()->getRegion());
        $address->setCountry($brandSettings->getAddress()->getCountry());
        $address->setPostcode($brandSettings->getAddress()->getPostcode());

        $dto = new AppDto();
        $dto->setId((string) $brandSettings->getId());
        $dto->setCode($brandSettings->getCode());
        $dto->setName($brandSettings->getBrandName());
        $dto->setEmailAddress($brandSettings->getEmailAddress());
        $dto->setAddress($address);
        $dto->setIsDefault($brandSettings->getIsDefault());
        $dto->setTaxNumber($brandSettings->getTaxNumber());
        $dto->setTaxRate($brandSettings->getTaxRate());
        $dto->setDigitalServicesTaxRate($brandSettings->getDigitalServicesRate());

        $dto->getNotifications()->setSubscriptionCreation($brandSettings->getNotificationSettings()->getSubscriptionCreation());
        $dto->getNotifications()->setSubscriptionCancellation($brandSettings->getNotificationSettings()->getSubscriptionCancellation());
        $dto->getNotifications()->setExpiringCardWarning($brandSettings->getNotificationSettings()->getExpiringCardWarning());
        $dto->getNotifications()->setExpiringCardDayBeforeWarning($brandSettings->getNotificationSettings()->getExpiringCardDayBefore());
        $dto->getNotifications()->setInvoiceCreated($brandSettings->getNotificationSettings()->getInvoiceCreated());
        $dto->getNotifications()->setQuoteCreated($brandSettings->getNotificationSettings()->getQuoteCreated());
        $dto->getNotifications()->setInvoiceOverdue($brandSettings->getNotificationSettings()->getInvoiceOverdue());

        return $dto;
    }
}

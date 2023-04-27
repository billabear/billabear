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

use App\Dto\Generic\Address as AddressDto;
use App\Dto\Generic\App\BrandSettings as AppDto;
use App\Dto\Request\App\BrandSettings\CreateBrandSettings;
use App\Dto\Request\App\BrandSettings\EditBrandSettings as EditDto;
use App\Entity\BrandSettings;
use Parthenon\Common\Address;

class BrandSettingsFactory
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

        return $brandSettings;
    }

    public function createAppDto(BrandSettings $brandSettings): AppDto
    {
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

        return $dto;
    }
}

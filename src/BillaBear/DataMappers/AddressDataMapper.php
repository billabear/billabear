<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\Address as Dto;
use Parthenon\Common\Address;

class AddressDataMapper
{
    public function createDto(Address $address): Dto
    {
        $dto = new Dto();
        $dto->setCompanyName($address->getCompanyName());
        $dto->setStreetLineOne($address->getStreetLineOne());
        $dto->setStreetLineTwo($address->getStreetLineTwo());
        $dto->setCity($address->getCity());
        $dto->setRegion($address->getRegion());
        $dto->setCountry($address->getCountry());
        $dto->setPostcode($address->getPostcode());

        return $dto;
    }
}

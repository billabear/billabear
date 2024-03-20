<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\Address as Dto;
use Parthenon\Common\Address;

class AddressDataMapper
{
    public function createDto(Address $address): Dto
    {
        $dto = new Dto();
        $dto->setStreetLineOne($address->getStreetLineOne());
        $dto->setStreetLineTwo($address->getStreetLineTwo());
        $dto->setCity($address->getCity());
        $dto->setRegion($address->getRegion());
        $dto->setCountry($address->getCountry());
        $dto->setPostcode($address->getPostcode());

        return $dto;
    }
}

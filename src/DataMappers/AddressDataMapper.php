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

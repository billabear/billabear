<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Export\Normaliser;

use App\Entity\Customer;
use Parthenon\Export\Normaliser\NormaliserInterface;

class CustomerNormaliser implements NormaliserInterface
{
    public function supports(mixed $item): bool
    {
        return $item instanceof Customer;
    }

    /**
     * @param Customer $item
     */
    public function normalise(mixed $item): array
    {
        return [
            'id' => (string) $item->getId(),
            'name' => $item->getName(),
            'email' => $item->getBillingEmail(),
            'country' => $item->getBillingAddress()?->getCountry(),
            'brand' => (string) $item->getBrandSettings()->getBrandName(),
            'created_at' => $item->getCreatedAt()->format(\DATE_ATOM),
        ];
    }
}

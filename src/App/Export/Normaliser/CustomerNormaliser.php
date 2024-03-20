<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

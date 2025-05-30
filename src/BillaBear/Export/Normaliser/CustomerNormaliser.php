<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Export\Normaliser;

use BillaBear\Entity\Customer;
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

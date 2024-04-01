<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy\Provider;

use Obol\Model\CreatePrice;
use Obol\Model\Price;
use Obol\Model\PriceCreation;
use Obol\PriceServiceInterface;

class PriceService implements PriceServiceInterface
{
    public function createPrice(CreatePrice $createPrice): PriceCreation
    {
        $priceCreation = new PriceCreation();
        $priceCreation->setReference(bin2hex(random_bytes(32)));

        return $priceCreation;
    }

    public function fetch(string $priceId): Price
    {
        // TODO: Implement fetch() method.
    }

    public function list(int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement list() method.
    }
}

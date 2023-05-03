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
}

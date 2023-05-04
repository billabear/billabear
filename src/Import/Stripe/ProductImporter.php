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

namespace App\Import\Stripe;

use App\Entity\StripeImport;
use App\Factory\ProductFactory;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;

class ProductImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private ProductRepositoryInterface $productRepository,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private ProductFactory $productFactory,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true)
    {
    }
}

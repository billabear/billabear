<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class InvoiceLine
{
    public function __construct(
        public string $description,
        public string $currency,
        public int $total,
        #[SerializedName('tax_total')]
        public int $taxTotal,
        #[SerializedName('sub_total')]
        public int $subTotal,
        #[SerializedName('tax_rate')]
        public ?float $taxRate,
    ) {
    }
}

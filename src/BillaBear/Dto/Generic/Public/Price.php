<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Price
{
    public function __construct(
        #[SerializedName('id')]
        public string $id,
        #[SerializedName('amount')]
        public ?int $amount,
        #[SerializedName('currency')]
        public string $currency,
        #[SerializedName('recurring')]
        public bool $recurring,
        #[SerializedName('schedule')]
        public ?string $schedule,
        #[SerializedName('including_tax')]
        public bool $includingTax,
    ) {
    }
}

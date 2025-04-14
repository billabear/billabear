<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class BillingAdmin
{
    public function __construct(
        public string $id,
        #[SerializedName('display_name')]
        public string $displayName,
    ) {
    }
}

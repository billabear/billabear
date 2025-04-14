<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App\Usage;

use BillaBear\Pricing\Usage\WarningLevel;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class UsageLimit
{
    public function __construct(
        public string $id,
        public int $amount,
        #[SerializedName('warn_level')]
        public WarningLevel $warnLevel,
    ) {
    }
}

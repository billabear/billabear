<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Feature
{
    public function __construct(
        #[SerializedName('code')]
        public string $code,
        #[SerializedName('name')]
        public string $name,
        #[SerializedName('description')]
        public ?string $description,
    ) {
    }
}

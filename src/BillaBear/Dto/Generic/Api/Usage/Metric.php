<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api\Usage;

use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class Metric
{
    public function __construct(
        public string $id,
        public string $name,
        public string $code,
        #[SerializedName('aggregation_method')]
        public string $aggregationMethod,
        #[SerializedName('aggregation_property')]
        public ?string $aggregationProperty,
        public array $filters,
        public \DateTime $createdAt,
    ) {
    }
}

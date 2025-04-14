<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App\Usage;

use BillaBear\Pricing\Usage\MetricAggregationMethod;
use BillaBear\Pricing\Usage\MetricEventIngestion;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class Metric
{
    public function __construct(
        public string $id,
        public string $name,
        public string $code,
        #[SerializedName('aggregation_method')]
        public MetricAggregationMethod $aggregationMethod,
        #[SerializedName('aggregation_property')]
        public ?string $aggregationProperty,
        #[SerializedName('event_ingestion')]
        public MetricEventIngestion $eventIngestion,
        public array $filters,
        #[SerializedName('created_at')]
        public \DateTime $createdAt,
    ) {
    }
}

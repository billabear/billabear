<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Usage;

use BillaBear\Dto\Generic\App\Usage\MetricCounter as AppDto;
use BillaBear\Entity\Subscription;
use BillaBear\Pricing\Usage\CostEstimator;

class MetricCounterDataMapper
{
    public function __construct(
        private MetricDataMapper $metricDataMapper,
        private CostEstimator $costEstimator,
    ) {
    }

    public function createAppDto(Subscription $entity): AppDto
    {
        $metric = $entity->getPrice()->getMetric();
        $costs = $this->costEstimator->getEstimate($entity);

        return new AppDto(
            (string) $metric->getId(),
            $costs->usage,
            $costs->cost->getMinorAmount()->toInt(),
            $costs->cost->getCurrency()->getCurrencyCode(),
            $this->metricDataMapper->createAppDto($metric),
        );
    }
}

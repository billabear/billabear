<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Usage;

use BillaBear\Dto\Generic\App\Usage\MetricCounter as AppDto;
use BillaBear\Entity\Subscription;
use BillaBear\Invoice\Usage\CostEstimator;

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

        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setMetric($this->metricDataMapper->createAppDto($metric));

        $costs = $this->costEstimator->getEstimate($entity);
        $dto->setUsage($costs->usage);
        $dto->setEstimatedCost($costs->cost->getMinorAmount()->toInt());
        $dto->setCurrency($costs->cost->getCurrency()->getCurrencyCode());

        return $dto;
    }
}

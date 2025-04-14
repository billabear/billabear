<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Usage;

use BillaBear\Dto\Generic\Api\Usage\Metric as ApiDto;
use BillaBear\Dto\Generic\Api\Usage\MetricFilter as ApiFilterDto;
use BillaBear\Dto\Generic\App\Usage\Metric as AppDto;
use BillaBear\Dto\Generic\App\Usage\MetricFilter as AppFilterDto;
use BillaBear\Dto\Request\App\Usage\CreateMetric;
use BillaBear\Dto\Request\App\Usage\UpdateMetric;
use BillaBear\Entity\Usage\Metric as Entity;
use BillaBear\Entity\Usage\MetricFilter as FilterEntity;
use BillaBear\Pricing\Usage\MetricAggregationMethod;
use BillaBear\Pricing\Usage\MetricEventIngestion;
use BillaBear\Pricing\Usage\MetricFilterType;

class MetricDataMapper
{
    public function createEntity(CreateMetric|UpdateMetric $dto, ?Entity $entity = null): Entity
    {
        if (!$entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime());
        }

        $entity->setName($dto->getName());
        if ($dto instanceof CreateMetric) {
            $entity->setCode($dto->getCode());
        }
        $entity->setAggregationMethod(MetricAggregationMethod::from($dto->getAggregationMethod()));
        $entity->setAggregationProperty($dto->getAggregationProperty());
        $entity->setEventIngestion(MetricEventIngestion::REAL_TIME);

        $filters = [];
        foreach ($dto->getFilters() as $createMetricFilter) {
            $filter = new FilterEntity();
            $filter->setMetric($entity);
            $filter->setName($createMetricFilter->getName());
            $filter->setValue($createMetricFilter->getValue());
            $filterType = MetricFilterType::from($createMetricFilter->getType());
            $filter->setType($filterType);
            $filters[] = $filter;
        }
        $entity->setFilters($filters);

        return $entity;
    }

    public function createAppDto(?Entity $entity): ?AppDto
    {
        if (!$entity) {
            return null;
        }

        return new AppDto(
            (string) $entity->getId(),
            $entity->getName(),
            $entity->getCode(),
            $entity->getAggregationMethod(),
            $entity->getAggregationProperty(),
            $entity->getEventIngestion(),
            array_map([$this, 'createFilterDto'], $entity->getFilters()->toArray()),
            $entity->getCreatedAt(),
        );
    }

    public function createApiDto(?Entity $entity): ?ApiDto
    {
        if (!$entity) {
            return null;
        }

        $dto = new ApiDto(
            (string) $entity->getId(),
            $entity->getName(),
            $entity->getCode(),
            $entity->getAggregationMethod()->value,
            $entity->getAggregationProperty(),
            array_map([$this, 'createApiFilterDto'], $entity->getFilters()->toArray()),
            $entity->getCreatedAt(),
        );

        return $dto;
    }

    private function createFilterDto(FilterEntity $filter): AppFilterDto
    {
        return new AppFilterDto(
            $filter->getName(),
            $filter->getValue(),
            $filter->getType()->value,
        );
    }

    private function createApiFilterDto(FilterEntity $filter): ApiFilterDto
    {
        $dto = new ApiFilterDto(
            (string) $filter->getId(),
            $filter->getName(),
            $filter->getValue(),
            $filter->getType()->value,
        );

        return $dto;
    }
}

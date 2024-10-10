<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
use BillaBear\Enum\MetricAggregationMethod;
use BillaBear\Enum\MetricEventIngestion;
use BillaBear\Enum\MetricFilterType;

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

        $dto = new AppDto();
        $dto->setName($entity->getName());
        $dto->setCode($entity->getCode());
        $dto->setAggregationMethod($entity->getAggregationMethod());
        $dto->setAggregationProperty($entity->getAggregationProperty());
        $dto->setEventIngestion($entity->getEventIngestion());
        $dto->setId((string) $entity->getId());
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setFilters(array_map([$this, 'createFilterDto'], $entity->getFilters()->toArray()));

        return $dto;
    }

    private function createFilterDto(FilterEntity $filter): AppFilterDto
    {
        $dto = new AppFilterDto();
        $dto->setName($filter->getName());
        $dto->setValue($filter->getValue());
        $dto->setType($filter->getType()->value);

        return $dto;
    }

    public function createApiDto(?Entity $entity): ?ApiDto
    {
        if (!$entity) {
            return null;
        }

        $dto = new ApiDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setCode($entity->getCode());
        $dto->setAggregationMethod($entity->getAggregationMethod()->value);
        $dto->setAggregationProperty($entity->getAggregationProperty());
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setFilters(array_map([$this, 'createApiFilterDto'], $entity->getFilters()->toArray()));

        return $dto;
    }

    private function createApiFilterDto(FilterEntity $filter): ApiFilterDto
    {
        $dto = new ApiFilterDto();
        $dto->setName($filter->getName());
        $dto->setValue($filter->getValue());
        $dto->setType($filter->getType()->value);

        return $dto;
    }
}

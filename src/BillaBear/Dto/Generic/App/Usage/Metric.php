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

class Metric
{
    private string $id;

    private string $name;

    private string $code;

    #[SerializedName('aggregation_method')]
    private MetricAggregationMethod $aggregationMethod;

    #[SerializedName('aggregation_property')]
    private ?string $aggregationProperty;

    #[SerializedName('event_ingestion')]
    private MetricEventIngestion $eventIngestion;

    private array $filters;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getAggregationMethod(): MetricAggregationMethod
    {
        return $this->aggregationMethod;
    }

    public function setAggregationMethod(MetricAggregationMethod $aggregationMethod): void
    {
        $this->aggregationMethod = $aggregationMethod;
    }

    public function getEventIngestion(): MetricEventIngestion
    {
        return $this->eventIngestion;
    }

    public function setEventIngestion(MetricEventIngestion $eventIngestion): void
    {
        $this->eventIngestion = $eventIngestion;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getAggregationProperty(): ?string
    {
        return $this->aggregationProperty;
    }

    public function setAggregationProperty(?string $aggregationProperty): void
    {
        $this->aggregationProperty = $aggregationProperty;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }
}

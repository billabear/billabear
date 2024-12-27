<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Usage;

use BillaBear\Pricing\Usage\MetricAggregationMethod;
use BillaBear\Pricing\Usage\MetricEventIngestion;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateMetric
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\Choice(choices: MetricAggregationMethod::METHODS_STRING)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('aggregation_method')]
    private $aggregationMethod;

    #[Assert\When(
        expression: 'this.getAggregationMethod() == "unique_count" || this.getAggregationMethod() == "sum_weighted" || this.getAggregationMethod() == "max"',
        constraints: [
            new Assert\NotBlank(),
            new Assert\Type('string'),
        ],
    )]
    #[SerializedName('aggregation_property')]
    private $aggregationProperty;

    #[Assert\Choice(choices: MetricEventIngestion::TYPES)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('event_ingestion')]
    private $eventIngestion;

    #[Assert\Type('array')]
    #[Assert\Valid]
    private array $filters = [];

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getAggregationMethod()
    {
        return $this->aggregationMethod;
    }

    public function setAggregationMethod($aggregationMethod): void
    {
        $this->aggregationMethod = $aggregationMethod;
    }

    public function getEventIngestion()
    {
        return $this->eventIngestion;
    }

    public function setEventIngestion($eventIngestion): void
    {
        $this->eventIngestion = $eventIngestion;
    }

    public function getAggregationProperty()
    {
        return $this->aggregationProperty;
    }

    public function setAggregationProperty($aggregationProperty): void
    {
        $this->aggregationProperty = $aggregationProperty;
    }

    /**
     * @return CreateMetricFilter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    public function addFilter(CreateMetricFilter $createMetricFilter): void
    {
        $this->filters[] = $createMetricFilter;
    }
}

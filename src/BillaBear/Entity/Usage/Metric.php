<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Usage;

use BillaBear\Pricing\Usage\MetricAggregationMethod;
use BillaBear\Pricing\Usage\MetricEventIngestion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
class Metric
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255, enumType: MetricAggregationMethod::class)]
    private MetricAggregationMethod $aggregationMethod;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $aggregationProperty = null;

    #[ORM\Column(type: 'string', length: 255, enumType: MetricEventIngestion::class)]
    private MetricEventIngestion $eventIngestion;

    #[ORM\OneToMany(targetEntity: MetricFilter::class, mappedBy: 'metric', cascade: ['persist'], orphanRemoval: true)]
    private array|Collection $filters;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
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

    public function getAggregationProperty(): ?string
    {
        return $this->aggregationProperty;
    }

    public function setAggregationProperty(?string $aggregationProperty): void
    {
        $this->aggregationProperty = $aggregationProperty;
    }

    public function getEventIngestion(): MetricEventIngestion
    {
        return $this->eventIngestion;
    }

    public function setEventIngestion(MetricEventIngestion $eventIngestion): void
    {
        $this->eventIngestion = $eventIngestion;
    }

    /**
     * @return MetricFilter[]
     */
    public function getFilters(): Collection
    {
        if (!isset($this->filters)) {
            return new ArrayCollection([]);
        }

        if (is_array($this->filters)) {
            return new ArrayCollection($this->filters);
        }

        return $this->filters;
    }

    public function setFilters(array|Collection $filters): void
    {
        $this->filters = $filters;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App\Usage;

use Symfony\Component\Serializer\Attribute\SerializedName;

class MetricCounter
{
    private string $id;

    private float $usage;

    #[SerializedName('estimated_cost')]
    private int $estimatedCost;

    private string $currency;

    private Metric $metric;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getUsage(): float
    {
        return $this->usage;
    }

    public function setUsage(float $usage): void
    {
        $this->usage = $usage;
    }

    public function getEstimatedCost(): int
    {
        return $this->estimatedCost;
    }

    public function setEstimatedCost(int $estimatedCost): void
    {
        $this->estimatedCost = $estimatedCost;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getMetric(): Metric
    {
        return $this->metric;
    }

    public function setMetric(Metric $metric): void
    {
        $this->metric = $metric;
    }
}

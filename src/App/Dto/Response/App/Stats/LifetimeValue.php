<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Stats;

use Symfony\Component\Serializer\Annotation\SerializedName;

class LifetimeValue
{
    #[SerializedName('lifetime_value')]
    private int $lifetimeValue;

    private float $lifespan;

    private string $currency;

    #[SerializedName('customer_count')]
    private int $customerCount;

    private array $brands;

    private array $plans;

    #[SerializedName('graph_data')]
    private array $graphData = [];

    public function getLifetimeValue(): int
    {
        return $this->lifetimeValue;
    }

    public function setLifetimeValue(int $lifetimeValue): void
    {
        $this->lifetimeValue = $lifetimeValue;
    }

    public function getLifespan(): float
    {
        return $this->lifespan;
    }

    public function setLifespan(float $lifespan): void
    {
        $this->lifespan = $lifespan;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCustomerCount(): int
    {
        return $this->customerCount;
    }

    public function setCustomerCount(int $customerCount): void
    {
        $this->customerCount = $customerCount;
    }

    public function getBrands(): array
    {
        return $this->brands;
    }

    public function setBrands(array $brands): void
    {
        $this->brands = $brands;
    }

    public function getPlans(): array
    {
        return $this->plans;
    }

    public function setPlans(array $plans): void
    {
        $this->plans = $plans;
    }

    public function getGraphData(): array
    {
        return $this->graphData;
    }

    public function setGraphData(array $graphData): void
    {
        $this->graphData = $graphData;
    }
}

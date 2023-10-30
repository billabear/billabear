<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Subscription;

use BillaBear\Dto\Generic\App\Usage\Metric;

class UsageEstimate
{
    private Metric $metric;

    private int $amount;

    private float $usage;

    public function getMetric(): Metric
    {
        return $this->metric;
    }

    public function setMetric(Metric $metric): void
    {
        $this->metric = $metric;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getUsage(): float
    {
        return $this->usage;
    }

    public function setUsage(float $usage): void
    {
        $this->usage = $usage;
    }
}

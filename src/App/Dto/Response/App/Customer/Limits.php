<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Customer;

class Limits
{
    private array $limits;

    private array $features;

    private int $userCount;

    public function getLimits(): array
    {
        return $this->limits;
    }

    public function setLimits(array $limits): void
    {
        $this->limits = $limits;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    public function getUserCount(): int
    {
        return $this->userCount;
    }

    public function setUserCount(int $userCount): void
    {
        $this->userCount = $userCount;
    }
}

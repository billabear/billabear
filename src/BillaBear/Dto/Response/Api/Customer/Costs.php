<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\Api\Customer;

class Costs
{
    private TotalCost $total;
    private array $costs;

    public function getTotal(): TotalCost
    {
        return $this->total;
    }

    public function setTotal(TotalCost $total): void
    {
        $this->total = $total;
    }

    public function getCosts(): array
    {
        return $this->costs;
    }

    public function setCosts(array $costs): void
    {
        $this->costs = $costs;
    }
}

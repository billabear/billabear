<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\Api\Customer;

class Costs
{
    private Cost $total;
    private array $costs;

    public function getTotal(): Cost
    {
        return $this->total;
    }

    public function setTotal(Cost $total): void
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

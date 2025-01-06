<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Tax;

use BillaBear\Dto\Generic\App\State;
use Symfony\Component\Serializer\Attribute\SerializedName;

class StateView
{
    private State $state;

    #[SerializedName('tax_rules')]
    private array $taxRules = [];

    #[SerializedName('tax_types')]
    private array $taxTypes = [];

    public function getState(): State
    {
        return $this->state;
    }

    public function setState(State $state): void
    {
        $this->state = $state;
    }

    public function getTaxRules(): array
    {
        return $this->taxRules;
    }

    public function setTaxRules(array $taxRules): void
    {
        $this->taxRules = $taxRules;
    }

    public function getTaxTypes(): array
    {
        return $this->taxTypes;
    }

    public function setTaxTypes(array $taxTypes): void
    {
        $this->taxTypes = $taxTypes;
    }
}

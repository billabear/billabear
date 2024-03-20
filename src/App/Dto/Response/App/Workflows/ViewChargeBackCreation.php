<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Workflows;

use App\Dto\Generic\App\Workflows\ChargeBackCreation;

class ViewChargeBackCreation
{
    private ChargeBackCreation $chargeBackCreation;

    public function getChargeBackCreation(): ChargeBackCreation
    {
        return $this->chargeBackCreation;
    }

    public function setChargeBackCreation(ChargeBackCreation $chargeBackCreation): void
    {
        $this->chargeBackCreation = $chargeBackCreation;
    }
}

<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Workflows;

use BillaBear\Dto\Generic\App\Workflows\ChargeBackCreation;

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

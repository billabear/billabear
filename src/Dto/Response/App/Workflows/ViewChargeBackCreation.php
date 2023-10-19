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

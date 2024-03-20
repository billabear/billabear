<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App;

use App\Dto\Generic\App\Refund;

class RefundView
{
    private Refund $refund;

    public function getRefund(): Refund
    {
        return $this->refund;
    }

    public function setRefund(Refund $refund): void
    {
        $this->refund = $refund;
    }
}

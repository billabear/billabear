<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Workflows;

use BillaBear\Dto\Generic\App\Workflows\RefundCreatedProcess;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewRefundCreatedProcess
{
    #[SerializedName('refund_created_process')]
    private RefundCreatedProcess $refundCreatedProcess;

    public function getRefundCreatedProcess(): RefundCreatedProcess
    {
        return $this->refundCreatedProcess;
    }

    public function setRefundCreatedProcess(RefundCreatedProcess $refundCreatedProcess): void
    {
        $this->refundCreatedProcess = $refundCreatedProcess;
    }
}

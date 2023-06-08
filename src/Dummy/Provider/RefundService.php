<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dummy\Provider;

use Obol\Model\Refund;
use Obol\Model\Refund\IssueRefund;
use Obol\RefundServiceInterface;

class RefundService implements RefundServiceInterface
{
    public function issueRefund(IssueRefund $issueRefund): Refund
    {
        $refund = new Refund();
        $refund->setAmount($issueRefund->getAmount()->getMinorAmount()->toInt());
        $refund->setCurrency($issueRefund->getAmount()->getCurrency()->getCurrencyCode());
        $refund->setId(bin2hex(random_bytes(32)));
        $refund->setPaymentId($issueRefund->getPaymentExternalReference());

        return $refund;
    }

    public function list(int $limit = 10, string $lastId = null): array
    {
        // TODO: Implement list() method.
    }
}

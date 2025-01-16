<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Provider;

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

    public function list(int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement list() method.
    }
}

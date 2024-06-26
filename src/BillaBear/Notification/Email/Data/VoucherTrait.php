<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email\Data;

use BillaBear\Entity\Voucher;
use Parthenon\Billing\Entity\Subscription;

trait VoucherTrait
{
    protected function getVoucherData(?Voucher $voucher, Subscription $subscription): array
    {
        try {
            return [
                'has_voucher' => !is_null($voucher),
                'type' => $voucher?->getType()->value,
                'percentage' => $voucher?->getPercentage(),
                'amount' => $voucher?->getAmountForCurrency($subscription->getCurrency())->getAmount(),
                'currency' => $subscription->getCurrency(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}

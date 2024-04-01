<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy\Provider;

use Obol\Model\Voucher\Voucher;
use Obol\Model\Voucher\VoucherApplicationResponse;
use Obol\Model\Voucher\VoucherCreation;
use Obol\VoucherServiceInterface;

class VoucherService implements VoucherServiceInterface
{
    public function createVoucher(Voucher $voucher): VoucherCreation
    {
        $voucherCreation = new VoucherCreation();
        $voucherCreation->setId(bin2hex(random_bytes(8)));

        return $voucherCreation;
    }

    public function list(int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement list() method.
    }

    public function applyCoupon(string $customerReference, string $couponReference): VoucherApplicationResponse
    {
        // TODO: Implement applyCoupon() method.
    }
}

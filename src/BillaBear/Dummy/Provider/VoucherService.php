<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Provider;

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

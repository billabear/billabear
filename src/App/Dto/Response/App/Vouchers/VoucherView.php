<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Vouchers;

use App\Dto\Generic\App\Voucher;

class VoucherView
{
    private Voucher $voucher;

    private array $amounts;

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }

    public function setVoucher(Voucher $voucher): void
    {
        $this->voucher = $voucher;
    }

    public function getAmounts(): array
    {
        return $this->amounts;
    }

    public function setAmounts(array $amounts): void
    {
        $this->amounts = $amounts;
    }
}

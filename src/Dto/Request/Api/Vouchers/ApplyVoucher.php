<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\Api\Vouchers;

use App\Validator\Constraints\VoucherCodeExists;
use Symfony\Component\Validator\Constraints as Assert;

class ApplyVoucher
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[VoucherCodeExists]
    private $code;

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }
}

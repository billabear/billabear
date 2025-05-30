<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Vouchers;

use BillaBear\Validator\Constraints\VoucherCodeExists;
use Symfony\Component\Validator\Constraints as Assert;

class ApplyVoucher
{
    #[Assert\NotBlank]
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

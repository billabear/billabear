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

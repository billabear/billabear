<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting;

use BillaBear\Entity\VoucherApplication;
use BillaBear\Exception\Integrations\UnexpectedErrorException;

interface VoucherInterface
{
    /**
     * @throws UnexpectedErrorException
     */
    public function register(VoucherApplication $voucher): void;
}

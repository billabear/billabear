<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Compliance;

use BillaBear\Dto\Generic\App\Customer;
use BillaBear\Dto\Response\App\ListResponse;

readonly class CustomerList
{
    public function __construct(
        public Customer $customer,
        public ListResponse $list,
    ) {
    }
}

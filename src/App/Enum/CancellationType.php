<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Enum;

enum CancellationType: string
{
    case CUSTOMER_REQUEST = 'customer_request';
    case COMPANY_REQUEST = 'company_request';
    case BILLING_RELATED = 'billing_related';
}

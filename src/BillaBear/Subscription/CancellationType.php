<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

enum CancellationType: string
{
    case CUSTOMER_REQUEST = 'customer_request';
    case COMPANY_REQUEST = 'company_request';
    case BILLING_RELATED = 'billing_related';
}

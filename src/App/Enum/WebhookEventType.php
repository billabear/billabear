<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Enum;

enum WebhookEventType: string
{
    case PAYMENT_RECEIVED = 'payment_received';
    case CUSTOMER_CREATED = 'customer_created';
    case CUSTOMER_ENABLED = 'customer_enabled';
    case CUSTOMER_DISABLED = 'customer_disabled';
    case SUBSCRIPTION_CREATED = 'subscription_created';
}

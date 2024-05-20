<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Enum;

enum SlackNotificationEvent: string
{
    case CUSTOMER_CREATED = 'customer_created';
    case PAYMENT_PROCESSED = 'payment_processed';
    case PAYMENT_FAILED = 'payment_failed';
    case SUBSCRIPTION_CREATED = 'subscription_created';
    case SUBSCRIPTION_CANCELLED = 'subscription_cancelled';
}

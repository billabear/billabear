<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
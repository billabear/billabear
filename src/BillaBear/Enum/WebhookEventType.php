<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Enum;

enum WebhookEventType: string
{
    case PAYMENT_RECEIVED = 'payment_received';

    case CUSTOMER_CREATED = 'customer_created';
    case CUSTOMER_ENABLED = 'customer_enabled';
    case CUSTOMER_DISABLED = 'customer_disabled';
    case CUSTOMER_UPDATED = 'customer_updated';

    case SUBSCRIPTION_CREATED = 'subscription_created';
    case SUBSCRIPTION_PAUSED = 'subscription_paused';
    case SUBSCRIPTION_CANCELLED = 'subscription_cancelled';
    case SUBSCRIPTION_UPDATED = 'subscription_updated';

    case TRIAL_STARTED = 'trial_started';
    case TRIAL_EXTENDED = 'trial_extended';
    case TRIAL_ENDED = 'trial_ended';

    case PLAN_CREATED = 'plan_created';
    case PLAN_DELETED = 'plan_deleted';
    case PLAN_UPDATED = 'plan_updated';

    case PAYMENT_METHOD_ADDED = 'payment_method_added';
    case PAYMENT_METHOD_DELETED = 'payment_method_deleted';
    case PAYMENT_METHOD_EXPIRED = 'payment_method_expired';
}

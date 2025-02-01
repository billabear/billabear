<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound;

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

    case INTEGRATION_ACCOUNTING_FAILURE = 'integration_accounting_failure';
    case INTEGRATION_NEWSLETTER_FAILURE = 'integration_newsletter_failure';
    case INTEGRATION_CUSTOMER_SUPPORT_FAILURE = 'integration_customer_support_failure';
    case INTEGRATION_CRM_FAILURE = 'integration_crm_failure';

    case USAGE_WARNING_TRIGGERED = 'usage_warning_triggered';

    case TAX_COUNTRY_THRESHOLD_REACHED = 'tax_country_threshold_reached';
    case TAX_STATE_THRESHOLD_REACHED = 'tax_state_threshold_reached';
}

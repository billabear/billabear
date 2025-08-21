<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack;

enum SlackNotificationEvent: string
{
    case CUSTOMER_CREATED = 'customer_created';
    case PAYMENT_PROCESSED = 'payment_processed';
    case PAYMENT_FAILED = 'payment_failed';
    case SUBSCRIPTION_CREATED = 'subscription_created';
    case SUBSCRIPTION_CANCELLED = 'subscription_cancelled';
    case TRIAL_STARTED = 'trial_started';
    case TRIAL_ENDED = 'trial_ended';
    case TRIAL_CONVERTED = 'trial_converted';
    case USAGE_WARNING = 'usage_warning';
    case USAGE_DISABLE = 'usage_disable';
    case TAX_COUNTRY_THRESHOLD_REACHED = 'tax_country_threshold_reached';
    case TAX_STATE_THRESHOLD_REACHED = 'tax_state_threshold_reached';
    case WORKFLOW_FAILURE = 'workflow_failure';
}

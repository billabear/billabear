<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

enum CustomerSubscriptionEventType: string
{
    case ACTIVATED = 'ACTIVATED';
    case REACTIVATED = 'REACTIVATED';
    case CHURNED = 'CHURNED';
    case UPGRADED = 'UPGRADED';
    case DOWNGRADED = 'DOWNGRADED';
    case ADDON_ADDED = 'ADDON_ADDED';
    case ADDON_REMOVED = 'ADDON_REMOVED';
    case TRIAL_STARTED = 'TRIAL_STARTED';
    case TRIAL_CONVERTED = 'TRIAL_CONVERTED';
    case TRIAL_ENDED = 'TRIAL_ENDED';
}

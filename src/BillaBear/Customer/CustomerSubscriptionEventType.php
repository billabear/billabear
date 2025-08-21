<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

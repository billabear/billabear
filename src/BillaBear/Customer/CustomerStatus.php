<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

enum CustomerStatus: string
{
    case NEW = 'new';
    case ACTIVE = 'active';
    case DISABLED = 'disabled';
    case CHURNED = 'churned';
    case REACTIVATED = 'reactivated';
    case TRIAL_ACTIVE = 'trial_active';
    case TRIAL_ENDED = 'trial_ended';
    case UNKNOWN = 'unknown';
}

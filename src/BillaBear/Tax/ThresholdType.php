<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

enum ThresholdType: string
{
    case ROLLING = 'rolling';
    case CALENDAR = 'calendar';
    case ROLLING_QUARTER = 'rolling_quarter';
    case ROLLING_ACCOUNTING = 'rolling_accounting';
}

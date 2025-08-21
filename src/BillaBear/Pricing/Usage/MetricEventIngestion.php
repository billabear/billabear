<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage;

enum MetricEventIngestion: string
{
    case REAL_TIME = 'real_time';
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    public const TYPES = [
        self::REAL_TIME->value,
        self::HOURLY->value,
        self::DAILY->value,
    ];
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Enum;

enum MetricAggregationMethod: string
{
    public const METHODS_STRING = [
        self::COUNT->value,
        self::SUM->value,
        self::LATEST->value,
        self::UNIQUE_COUNT->value,
        self::MAX->value,
    ];

    case COUNT = 'count';
    case SUM = 'sum';
    case LATEST = 'latest';
    case UNIQUE_COUNT = 'unique_count';
    case MAX = 'max';
}

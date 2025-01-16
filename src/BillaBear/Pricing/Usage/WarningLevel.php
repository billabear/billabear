<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage;

enum WarningLevel: int
{
    case NO_WARNING = 0;
    case WARNING = 1000;
    case DISABLE = 9999;

    public static function fromName(string $name): static
    {
        foreach (static::cases() as $case) {
            if ($case->name === strtoupper($name)) {
                return $case;
            }
        }

        throw new \Exception('Unable to find value');
    }
}

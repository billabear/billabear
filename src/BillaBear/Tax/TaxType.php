<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

enum TaxType: string
{
    case DIGITAL_GOODS = 'digital_goods';
    case DIGITAL_SERVICES = 'digital_services';
    case PHYSICAL = 'physical';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromName(?string $name): self
    {
        if (!$name) {
            return self::DIGITAL_GOODS;
        }
        foreach (self::cases() as $status) {
            if ($name === $status->value) {
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }
}

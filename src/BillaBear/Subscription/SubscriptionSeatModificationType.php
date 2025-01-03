<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

enum SubscriptionSeatModificationType: string
{
    case ADDED = 'added';
    case REMOVED = 'removed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromName(?string $name): self
    {
        if (!$name) {
            return self::ADDED;
        }
        foreach (self::cases() as $status) {
            if ($name === $status->value) {
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }
}

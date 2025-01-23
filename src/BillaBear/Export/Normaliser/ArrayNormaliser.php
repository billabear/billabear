<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Export\Normaliser;

use Parthenon\Export\Normaliser\NormaliserInterface;

class ArrayNormaliser implements NormaliserInterface
{
    public function supports(mixed $item): bool
    {
        return is_array($item);
    }

    public function normalise(mixed $item): array
    {
        return $item;
    }
}

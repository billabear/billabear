<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App\Usage;

use Symfony\Component\Serializer\Attribute\SerializedName;

class UsageLimit
{
    private string $id;

    private int $amount;

    #[SerializedName('warn_level')]
    private int $warnLevel;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getWarnLevel(): int
    {
        return $this->warnLevel;
    }

    public function setWarnLevel(int $warnLevel): void
    {
        $this->warnLevel = $warnLevel;
    }
}

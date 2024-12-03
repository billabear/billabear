<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Usage;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUsageLimit
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    private $amount;

    #[SerializedName('warn_level')]
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Range(min: 0, max: 9000)]
    private $warnLevel;

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    public function getWarnLevel()
    {
        return $this->warnLevel;
    }

    public function setWarnLevel($warnLevel): void
    {
        $this->warnLevel = $warnLevel;
    }
}

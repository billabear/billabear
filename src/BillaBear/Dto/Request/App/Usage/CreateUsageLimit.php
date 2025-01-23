<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Usage;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUsageLimit
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type('integer')]
    private $amount;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 9000)]
    #[Assert\Type('integer')]
    #[SerializedName('warn_level')]
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

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Payments;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class RefundPayment
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type(type: 'integer')]
    #[SerializedName('amount')]
    private $amount;

    #[Assert\Currency]
    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('currency')]
    private $currency;

    #[Assert\NotBlank(allowNull: true)]
    private $reason;

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason($reason): void
    {
        $this->reason = $reason;
    }
}

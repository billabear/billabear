<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Payments;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class RefundPayment
{
    #[Assert\NotBlank()]
    #[Assert\Type(type: 'integer')]
    #[Assert\Positive]
    #[SerializedName('amount')]
    private $amount;

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

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason($reason): void
    {
        $this->reason = $reason;
    }
}

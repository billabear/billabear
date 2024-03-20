<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\CreditAdjustment;

use App\Entity\Credit;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCreditAdjustment
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [Credit::TYPE_CREDIT, Credit::TYPE_DEBIT])]
    private $type;

    #[Assert\NotBlank()]
    #[Assert\Positive]
    private $amount;

    #[Assert\NotBlank()]
    #[Assert\Currency]
    private $currency;

    #[Assert\NotBlank(allowNull: true)]
    private $reason;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

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

    public function getReason()
    {
        return $this->reason;
    }

    public function setReason($reason): void
    {
        $this->reason = $reason;
    }
}

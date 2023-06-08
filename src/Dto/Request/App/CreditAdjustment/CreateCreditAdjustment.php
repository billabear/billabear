<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

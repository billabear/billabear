<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Checkout;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCheckoutItem
{
    #[Assert\NotBlank]
    private $description;

    #[Assert\NotBlank]
    #[Assert\Positive]
    private $amount;

    #[Assert\Currency]
    private $currency;

    #[SerializedName('include_tax')]
    #[Assert\Type('bool')]
    private $includeTax;

    #[SerializedName('tax_type')]
    #[Assert\NotBlank()]
    #[Assert\Choice(choices: ['digital_goods', 'physical', 'digital_services'])]
    private $taxType;

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
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

    public function getIncludeTax()
    {
        return $this->includeTax;
    }

    public function setIncludeTax($includeTax): void
    {
        $this->includeTax = $includeTax;
    }

    public function getTaxType()
    {
        return $this->taxType;
    }

    public function setTaxType($taxType): void
    {
        $this->taxType = $taxType;
    }
}

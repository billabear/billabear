<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App;

use App\Validator\Constraints\ProductHasTax;
use App\Validator\Constraints\TaxTypeExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ProductHasTax]
class CreateProduct
{
    #[Assert\NotBlank()]
    #[SerializedName('name')]
    private string $name;

    #[SerializedName('external_reference')]
    private ?string $externalReference = null;

    #[SerializedName('tax_type')]
    #[Assert\NotBlank(allowNull: true)]
    #[TaxTypeExists]
    private $taxType;

    #[SerializedName('tax_rate')]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('numeric')]
    #[Assert\PositiveOrZero]
    private $taxRate;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function getTaxType()
    {
        return $this->taxType;
    }

    public function setTaxType($taxType): void
    {
        $this->taxType = $taxType;
    }

    public function getTaxRate()
    {
        return $this->taxRate;
    }

    public function setTaxRate($taxRate): void
    {
        $this->taxRate = $taxRate;
    }
}

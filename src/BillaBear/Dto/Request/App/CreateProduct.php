<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App;

use BillaBear\Validator\Constraints\ProductHasTax;
use BillaBear\Validator\Constraints\TaxTypeExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ProductHasTax]
readonly class CreateProduct
{
    public function __construct(
        #[Assert\NotBlank]
        #[SerializedName('name')]
        public string $name,

        #[SerializedName('external_reference')]
        public ?string $externalReference = null,

        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('tax_type')]
        #[TaxTypeExists]
        public ?string $taxType = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\PositiveOrZero]
        #[Assert\Type('numeric')]
        #[SerializedName('tax_rate')]
        public ?string $taxRate = null,

        #[Assert\Type('boolean')]
        public bool $physical = false,
    ) {
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Public;

use BillaBear\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateCustomerDto
{
    public function __construct(
        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('name')]
        public ?string $name = null,

        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('brand')]
        public ?string $brand = null,

        #[Assert\Email]
        #[Assert\NotBlank]
        #[SerializedName('email')]
        public ?string $email = null,

        #[Assert\Locale]
        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('locale')]
        public ?string $locale = null,

        #[SerializedName('reference')]
        public ?string $reference = null,

        #[SerializedName('external_reference')]
        public ?string $externalReference = null,

        #[Assert\Choice(choices: ['invoice', 'card'])]
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        #[SerializedName('billing_type')]
        public ?string $billingType = null,

        #[Assert\Valid]
        #[SerializedName('address')]
        public ?Address $address = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        #[SerializedName('tax_number')]
        public ?string $taxNumber = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\PositiveOrZero]
        #[Assert\Type(['integer', 'float'])]
        #[SerializedName('digital_tax_rate')]
        public ?float $digitalTaxRate = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\PositiveOrZero]
        #[Assert\Type(['integer', 'float'])]
        #[SerializedName('standard_tax_rate')]
        public ?float $standardTaxRate = null,

        #[Assert\Choice(choices: ['individual', 'business'])]
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $type = null,

        #[Assert\Choice(['pdf'])]
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        public ?string $invoiceFormat = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('boolean')]
        #[SerializedName('marketing_opt_in')]
        public ?bool $marketingOptIn = null,

        #[Assert\Type('array')]
        public ?array $metadata = null,
    ) {
    }
}

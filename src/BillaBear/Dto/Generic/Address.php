<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic;

use BillaBear\Validator\Constraints\Country\CountryIsEnabled;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class Address
{
    public function __construct(
        #[SerializedName('company_name')]
        public ?string $companyName = null,

        #[SerializedName('street_line_one')]
        public ?string $streetLineOne = null,

        #[SerializedName('street_line_two')]
        public ?string $streetLineTwo = null,

        #[SerializedName('city')]
        public ?string $city = null,

        #[SerializedName('region')]
        public ?string $region = null,

        #[Assert\Country]
        #[CountryIsEnabled]
        #[SerializedName('country')]
        public ?string $country = null,

        #[SerializedName('postcode')]
        public ?string $postcode = null,
    ) {
    }
}

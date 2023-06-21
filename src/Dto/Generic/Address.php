<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Generic;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    #[SerializedName('company_name')]
    private ?string $companyName = null;

    #[SerializedName('street_line_one')]
    private ?string $streetLineOne = null;

    #[SerializedName('street_line_two')]
    private ?string $streetLineTwo = null;

    #[SerializedName('city')]
    private ?string $city = null;

    #[SerializedName('region')]
    private ?string $region = null;

    #[SerializedName('country')]
    #[Assert\Country]
    private ?string $country = null;

    #[SerializedName('postcode')]
    private ?string $postcode = null;

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getStreetLineOne(): ?string
    {
        return $this->streetLineOne;
    }

    public function setStreetLineOne(?string $streetLineOne): void
    {
        $this->streetLineOne = $streetLineOne;
    }

    public function getStreetLineTwo(): ?string
    {
        return $this->streetLineTwo;
    }

    public function setStreetLineTwo(?string $streetLineTwo): void
    {
        $this->streetLineTwo = $streetLineTwo;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function getCountry(): ?string
    {
        if (!isset($this->country)) {
            return '';
        }

        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }
}

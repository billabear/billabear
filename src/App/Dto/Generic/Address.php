<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

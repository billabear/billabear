<?php

namespace App\Dto\Generic;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class Address
{
    #[SerializedName('company_name')]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $companyName = null;

    #[SerializedName('street_line_one')]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $streetLineOne = null;

    #[SerializedName('street_line_two')]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $streetLineTwo = null;

    #[SerializedName('city')]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $city = null;

    #[SerializedName('region')]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $region = null;

    #[SerializedName('country')]
    #[Assert\NotBlank]
    #[Assert\Country]
    private ?string $country = null;

    #[SerializedName('post_code')]
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

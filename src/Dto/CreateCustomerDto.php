<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCustomerDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[SerializedName('email')]
    private string $email;

    #[SerializedName('reference')]
    private ?string $reference = null;

    #[SerializedName('external_reference')]
    private ?string $externalReference = null;

    #[Assert\NotBlank]
    #[SerializedName('country')]
    private string $country;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }
}

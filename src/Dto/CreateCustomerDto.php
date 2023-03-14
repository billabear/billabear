<?php

namespace App\Dto;

use App\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCustomerDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[SerializedName('email')]
    private ?string $email;

    #[SerializedName('reference')]
    private ?string $reference = null;

    #[SerializedName('external_reference')]
    private ?string $externalReference = null;

    #[Assert\Valid]
    #[SerializedName('address')]
    private Address $address;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
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

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\BrandSettings;

use BillaBear\Validator\Constraints\UniqueBrandCode;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateBrandSettings
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Regex('~[a-z0-9_]+~', message: 'Code must be lower case alphanumeric with underscores only')]
    #[Assert\Type('string')]
    #[UniqueBrandCode]
    private $code;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('email_address')]
    private $emailAddress;

    #[Assert\Valid]
    private Address $address;

    private Notifications $notifications;

    #[Assert\Type('string')]
    #[SerializedName('tax_number')]
    private $taxNumber;

    #[Assert\Type('numeric')]
    #[SerializedName('tax_rate')]
    private $taxRate;

    #[Assert\Type('numeric')]
    #[SerializedName('digital_services_tax_rate')]
    private $digitalServicesTaxRate;

    #[Assert\Email]
    #[Assert\Type('string')]
    #[SerializedName('support_email_address')]
    private $supportEmailAddress;

    #[Assert\Type('string')]
    #[SerializedName('support_phone_number')]
    private $supportPhoneNumber;

    public function __construct()
    {
        $this->notifications = new Notifications();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function setEmailAddress($emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getNotifications(): Notifications
    {
        return $this->notifications;
    }

    public function setNotifications(Notifications $notifications): void
    {
        $this->notifications = $notifications;
    }

    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    public function setTaxNumber($taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getTaxRate()
    {
        return $this->taxRate;
    }

    public function setTaxRate($taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    public function getDigitalServicesTaxRate()
    {
        return $this->digitalServicesTaxRate;
    }

    public function setDigitalServicesTaxRate($digitalServicesTaxRate): void
    {
        $this->digitalServicesTaxRate = $digitalServicesTaxRate;
    }

    public function getSupportEmailAddress()
    {
        return $this->supportEmailAddress;
    }

    public function setSupportEmailAddress($supportEmailAddress): void
    {
        $this->supportEmailAddress = $supportEmailAddress;
    }

    public function getSupportPhoneNumber()
    {
        return $this->supportPhoneNumber;
    }

    public function setSupportPhoneNumber($supportPhoneNumber): void
    {
        $this->supportPhoneNumber = $supportPhoneNumber;
    }
}

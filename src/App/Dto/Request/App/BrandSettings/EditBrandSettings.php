<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\BrandSettings;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class EditBrandSettings
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Email]
    #[SerializedName('email_address')]
    private $emailAddress;

    #[Assert\Valid]
    private Address $address;

    private Notifications $notifications;

    #[SerializedName('tax_number')]
    #[Assert\Type('string')]
    private $taxNumber;

    #[Assert\Type(['numeric'])]
    #[SerializedName('tax_rate')]
    private $taxRate;

    #[Assert\Type('numeric')]
    #[SerializedName('digital_services_tax_rate')]
    private $digitalServicesTaxRate;

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
}

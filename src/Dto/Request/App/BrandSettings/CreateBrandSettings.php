<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\BrandSettings;

use App\Validator\Constraints\UniqueBrandCode;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateBrandSettings
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Regex('~[a-z0-9_]+~', message: 'Code must be lower case alphanumeric with underscores only')]
    #[UniqueBrandCode]
    private $code;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Email]
    #[SerializedName('email_address')]
    private $emailAddress;

    #[Assert\Valid]
    private Address $address;

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
}
<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Country;

use App\Validator\Constraints\Country\UniqueCountryCode;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCountry
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[SerializedName('iso_code')]
    #[Assert\NotBlank]
    #[Assert\Country]
    #[UniqueCountryCode]
    private $isoCode;

    #[Assert\NotBlank]
    #[Assert\Currency]
    private $currency;

    #[Assert\Type('integer')]
    #[Assert\PositiveOrZero]
    private $threshold;

    #[Assert\NotBlank()]
    #[Assert\Type('boolean')]
    private $default;

    #[SerializedName('in_eu')]
    #[Assert\NotBlank()]
    #[Assert\Type('boolean')]
    private $inEu = false;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getIsoCode()
    {
        return $this->isoCode;
    }

    public function setIsoCode($isoCode): void
    {
        $this->isoCode = $isoCode;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    public function getThreshold()
    {
        return $this->threshold;
    }

    public function setThreshold($threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getInEu()
    {
        return $this->inEu;
    }

    public function setInEu($inEu): void
    {
        $this->inEu = $inEu;
    }
}

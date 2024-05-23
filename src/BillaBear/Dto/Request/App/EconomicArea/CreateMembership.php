<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\EconomicArea;

use BillaBear\Validator\Constraints\Country\CountryExists;
use BillaBear\Validator\Constraints\EconomicArea\EconomicAreaExists;
use BillaBear\Validator\Constraints\EconomicArea\NotMemberOfEconomicArea;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[NotMemberOfEconomicArea]
class CreateMembership
{
    #[Assert\NotBlank]
    #[Assert\Uuid()]
    #[EconomicAreaExists]
    #[SerializedName('economic_area')]
    private $economicArea;

    #[Assert\NotBlank]
    #[Assert\Uuid()]
    #[CountryExists]
    private $country;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: \DATE_RFC3339_EXTENDED)]
    #[SerializedName('joined_at')]
    private $joinedAt;

    #[Assert\DateTime(format: \DATE_RFC3339_EXTENDED)]
    #[SerializedName('left')]
    private $leftAt;

    public function getEconomicArea()
    {
        return $this->economicArea;
    }

    public function setEconomicArea($economicArea): void
    {
        $this->economicArea = $economicArea;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): void
    {
        $this->country = $country;
    }

    public function getJoinedAt()
    {
        return $this->joinedAt;
    }

    public function setJoinedAt($joinedAt): void
    {
        $this->joinedAt = $joinedAt;
    }

    public function getLeftAt()
    {
        return $this->leftAt;
    }

    public function setLeftAt($leftAt): void
    {
        $this->leftAt = $leftAt;
    }
}

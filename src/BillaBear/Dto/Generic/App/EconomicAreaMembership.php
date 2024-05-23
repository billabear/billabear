<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Attribute\SerializedName;

class EconomicAreaMembership
{
    private string $id;

    private Country $country;

    #[SerializedName('joined_at')]
    private \DateTime $joinedAt;

    #[SerializedName('left_at')]
    private ?\DateTime $leftAt = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function getJoinedAt(): \DateTime
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTime $joinedAt): void
    {
        $this->joinedAt = $joinedAt;
    }

    public function getLeftAt(): ?\DateTime
    {
        return $this->leftAt;
    }

    public function setLeftAt(?\DateTime $leftAt): void
    {
        $this->leftAt = $leftAt;
    }
}

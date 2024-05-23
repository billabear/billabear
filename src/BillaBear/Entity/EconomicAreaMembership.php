<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'economic_area_member')]
class EconomicAreaMembership
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    private Country $country;

    #[ORM\ManyToOne(targetEntity: EconomicArea::class, inversedBy: 'members')]
    private EconomicArea $economicArea;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $joinedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $leftAt = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
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

    public function getEconomicArea(): EconomicArea
    {
        return $this->economicArea;
    }

    public function setEconomicArea(EconomicArea $economicArea): void
    {
        $this->economicArea = $economicArea;
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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

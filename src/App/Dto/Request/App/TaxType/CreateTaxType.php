<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\TaxType;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaxType
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank()]
    #[Assert\Type('boolean')]
    private $physical;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getPhysical()
    {
        return $this->physical;
    }

    public function setPhysical($physical): void
    {
        $this->physical = $physical;
    }
}

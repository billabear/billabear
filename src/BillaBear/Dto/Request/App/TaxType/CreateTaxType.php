<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\TaxType;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaxType
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\Type('string')]
    private $vatSenseType;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getVatSenseType()
    {
        return $this->vatSenseType;
    }

    public function setVatSenseType($vatSenseType): void
    {
        $this->vatSenseType = $vatSenseType;
    }
}

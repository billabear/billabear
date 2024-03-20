<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App;

use App\Validator\Constraints\UniqueFeature;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class PostFeature
{
    #[Assert\NotBlank()]
    #[UniqueFeature]
    #[SerializedName('code')]
    private $code;

    #[Assert\NotBlank()]
    #[SerializedName('name')]
    private $name;

    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('description')]
    private $description;

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }
}

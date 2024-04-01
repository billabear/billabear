<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Settings;

use App\Validator\Constraints\InTheFuture;
use App\Validator\Constraints\UniqueApiKeyName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateApiKey
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[UniqueApiKeyName]
    private $name;

    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    #[Assert\NotBlank()]
    #[InTheFuture]
    private $expiresAt;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function setExpiresAt($expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}

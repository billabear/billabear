<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings;

use BillaBear\Validator\Constraints\InTheFuture;
use BillaBear\Validator\Constraints\UniqueApiKeyName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateApiKey
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[UniqueApiKeyName]
    private $name;

    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    #[Assert\NotBlank]
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

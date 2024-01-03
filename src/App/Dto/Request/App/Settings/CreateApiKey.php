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

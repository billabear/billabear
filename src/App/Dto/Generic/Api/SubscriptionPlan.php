<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SubscriptionPlan
{
    private $id;

    private string $name;

    #[SerializedName('code_name')]
    private ?string $codeName;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCodeName(): ?string
    {
        return $this->codeName;
    }

    public function setCodeName(?string $codeName): void
    {
        $this->codeName = $codeName;
    }
}

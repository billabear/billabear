<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Generic\App\Workflows;

use App\Dto\Generic\App\ChargeBack;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ChargeBackCreation
{
    private string $id;

    private string $state;

    private string $error;

    #[SerializedName('charge_back')]
    private ChargeBack $chargeBack;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    #[SerializedName('updated_at')]
    private \DateTime $updatedAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function getChargeBack(): ChargeBack
    {
        return $this->chargeBack;
    }

    public function setChargeBack(ChargeBack $chargeBack): void
    {
        $this->chargeBack = $chargeBack;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
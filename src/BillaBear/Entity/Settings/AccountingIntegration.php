<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class AccountingIntegration
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $enabled = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $integration = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $apiKey = null;

    public function getEnabled(): bool
    {
        return true === $this->enabled;
    }

    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getIntegration(): ?string
    {
        return $this->integration;
    }

    public function setIntegration(?string $integration): void
    {
        $this->integration = $integration;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}

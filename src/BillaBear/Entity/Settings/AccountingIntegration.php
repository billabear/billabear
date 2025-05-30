<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

    #[ORM\Embedded(class: OauthSettings::class)]
    private OauthSettings $oauthSettings;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $settings = null;

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

    public function getOauthSettings(): OauthSettings
    {
        return $this->oauthSettings;
    }

    public function setOauthSettings(OauthSettings $oauthSettings): void
    {
        $this->oauthSettings = $oauthSettings;
    }

    public function getSettings(): array
    {
        if (!isset($this->settings)) {
            return [];
        }

        return $this->settings;
    }

    public function setSettings(?array $settings): void
    {
        $this->settings = $settings;
    }
}

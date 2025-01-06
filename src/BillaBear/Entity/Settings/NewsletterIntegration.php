<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class NewsletterIntegration
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $enabled = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $integration = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $marketingListId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $announcementListId = null;

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

    public function getMarketingListId(): ?string
    {
        return $this->marketingListId;
    }

    public function setMarketingListId(?string $marketingListId): void
    {
        $this->marketingListId = $marketingListId;
    }

    public function getAnnouncementListId(): ?string
    {
        return $this->announcementListId;
    }

    public function setAnnouncementListId(?string $announcementListId): void
    {
        $this->announcementListId = $announcementListId;
    }
}

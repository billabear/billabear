<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class SystemSettings
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $webhookUrl = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $timezone = null;

    #[ORM\Column(type: 'boolean')]
    private bool $useStripeBilling = true;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $webhookExternalReference = null;

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(?string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function isUseStripeBilling(): bool
    {
        return $this->useStripeBilling;
    }

    public function setUseStripeBilling(bool $useStripeBilling): void
    {
        $this->useStripeBilling = $useStripeBilling;
    }

    public function getWebhookExternalReference(): ?string
    {
        return $this->webhookExternalReference;
    }

    public function setWebhookExternalReference(?string $webhookExternalReference): void
    {
        $this->webhookExternalReference = $webhookExternalReference;
    }
}

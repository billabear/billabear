<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class NotificationSettings
{
    public const EMSP_SYSTEM = 'system';
    public const EMSP_SENDGRID = 'sendgrid';
    public const EMSP_POSTMARK = 'postmark';
    public const EMSP_MAILGUN = 'mailgun';
    public const EMSP_CHOICES = [
        self::EMSP_SYSTEM,
        self::EMSP_MAILGUN,
        self::EMSP_POSTMARK,
        self::EMSP_SENDGRID,
    ];

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $sendCustomerNotifications = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $emsp = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $emspApiKey = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $emspApiUrl = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $emspDomain = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $defaultOutgoingEmail = null;

    public function getSendCustomerNotifications(): ?bool
    {
        return $this->sendCustomerNotifications;
    }

    public function setSendCustomerNotifications(?bool $sendCustomerNotifications): void
    {
        $this->sendCustomerNotifications = $sendCustomerNotifications;
    }

    public function getEmsp(): ?string
    {
        return $this->emsp;
    }

    public function setEmsp(?string $emsp): void
    {
        $this->emsp = $emsp;
    }

    public function getEmspApiKey(): ?string
    {
        return $this->emspApiKey;
    }

    public function setEmspApiKey(?string $emspApiKey): void
    {
        $this->emspApiKey = $emspApiKey;
    }

    public function getEmspApiUrl(): ?string
    {
        return $this->emspApiUrl;
    }

    public function setEmspApiUrl(?string $emspApiUrl): void
    {
        $this->emspApiUrl = $emspApiUrl;
    }

    public function getEmspDomain(): ?string
    {
        return $this->emspDomain;
    }

    public function setEmspDomain(?string $emspDomain): void
    {
        $this->emspDomain = $emspDomain;
    }

    public function getDefaultOutgoingEmail(): ?string
    {
        return $this->defaultOutgoingEmail;
    }

    public function setDefaultOutgoingEmail(?string $defaultOutgoingEmail): void
    {
        $this->defaultOutgoingEmail = $defaultOutgoingEmail;
    }
}

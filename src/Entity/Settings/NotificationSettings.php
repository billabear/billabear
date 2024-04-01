<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

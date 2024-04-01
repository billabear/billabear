<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class NotificationSettings
{
    #[SerializedName('send_customer_notifications')]
    private ?bool $sendCustomerNotifications;

    private ?string $emsp;

    #[SerializedName('emsp_api_key')]
    private ?string $emspApiKey;

    #[SerializedName('emsp_api_url')]
    private ?string $emspApiUrl;

    #[SerializedName('emsp_domain')]
    private ?string $emspDomain;

    #[SerializedName('default_outgoing_email')]
    private ?string $defaultOutgoingEmail;

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

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class OauthSettings
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $stateSecret = null;

    #[ORM\Column(type: 'string', nullable: true, length: 2000)]
    private ?string $accessToken = null;

    #[ORM\Column(type: 'string', nullable: true, length: 2000)]
    private ?string $refreshToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $expiresAt = null;

    public function getStateSecret(): ?string
    {
        return $this->stateSecret;
    }

    public function setStateSecret(?string $stateSecret): void
    {
        $this->stateSecret = $stateSecret;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}

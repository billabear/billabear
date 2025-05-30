<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Dto;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class InstallRequest
{
    #[Assert\NotBlank]
    #[SerializedName('default_brand')]
    private $defaultBrand;

    #[Assert\Country]
    #[Assert\NotBlank]
    private $country;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[SerializedName('from_email')]
    private $fromEmail;

    #[Assert\NotBlank]
    #[Assert\Timezone]
    private $timezone;

    #[Assert\NotBlank]
    #[Assert\Url]
    #[SerializedName('webhook_url')]
    private $webhookUrl;

    #[Assert\Email]
    #[Assert\NotBlank]
    private $email;

    #[Assert\NotBlank]
    private $password;

    #[Assert\Currency]
    #[Assert\NotBlank]
    private $currency;

    public function getDefaultBrand()
    {
        return $this->defaultBrand;
    }

    public function setDefaultBrand($defaultBrand): void
    {
        $this->defaultBrand = $defaultBrand;
    }

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function setFromEmail($fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getWebhookUrl()
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl($webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): void
    {
        $this->country = $country;
    }
}

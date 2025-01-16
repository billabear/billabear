<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Developer\Webhook;

use Symfony\Component\Validator\Constraints as Assert;

class CreateWebhookEndpoint
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Url]
    protected $url;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url): void
    {
        $this->url = $url;
    }
}

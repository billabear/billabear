<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Developer\Webhook;

use Symfony\Component\Validator\Constraints as Assert;

class CreateWebhookEndpoint
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    protected $name;

    #[Assert\NotBlank()]
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

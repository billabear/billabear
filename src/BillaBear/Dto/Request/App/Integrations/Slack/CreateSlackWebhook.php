<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Integrations\Slack;

use BillaBear\Validator\Constraints\Integrations\SlackWebhookUnique;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSlackWebhook
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[SlackWebhookUnique]
    private $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Url]
    private $webhook;

    private $enabled;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getWebhook()
    {
        return $this->webhook;
    }

    public function setWebhook($webhook): void
    {
        $this->webhook = $webhook;
    }

    public function getEnabled()
    {
        return false !== $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }
}

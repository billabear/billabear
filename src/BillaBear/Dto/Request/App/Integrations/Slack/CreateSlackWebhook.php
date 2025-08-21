<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Integrations\Slack;

use BillaBear\Validator\Constraints\Integrations\SlackWebhookUnique;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSlackWebhook
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SlackWebhookUnique]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
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

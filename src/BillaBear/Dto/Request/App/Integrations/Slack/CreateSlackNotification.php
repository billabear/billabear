<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Integrations\Slack;

use BillaBear\Validator\Constraints\Integrations\SlackWebhookExists;
use BillaBear\Validator\Constraints\Integrations\ValidSlackEvent;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSlackNotification
{
    #[Assert\NotBlank]
    #[SlackWebhookExists]
    private $webhook;

    #[Assert\NotBlank]
    #[ValidSlackEvent]
    private $event;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $template;

    public function getWebhook()
    {
        return $this->webhook;
    }

    public function setWebhook($webhook): void
    {
        $this->webhook = $webhook;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event): void
    {
        $this->event = $event;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template): void
    {
        $this->template = $template;
    }
}

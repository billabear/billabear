<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App\Integrations;

use BillaBear\Enum\SlackNotificationEvent;

class SlackNotification
{
    private string $id;

    private SlackWebhook $webhook;

    private SlackNotificationEvent $event;

    private string $template;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getWebhook(): SlackWebhook
    {
        return $this->webhook;
    }

    public function setWebhook(SlackWebhook $webhook): void
    {
        $this->webhook = $webhook;
    }

    public function getEvent(): SlackNotificationEvent
    {
        return $this->event;
    }

    public function setEvent(SlackNotificationEvent $event): void
    {
        $this->event = $event;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }
}

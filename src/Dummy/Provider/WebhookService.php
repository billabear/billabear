<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy\Provider;

use Obol\Model\Events\EventInterface;
use Obol\Model\Webhook\WebhookCreation;
use Obol\Model\WebhookPayload;
use Obol\WebhookServiceInterface;

class WebhookService implements WebhookServiceInterface
{
    public function process(WebhookPayload $payload): ?EventInterface
    {
        return null;
    }

    public function registerWebhook(string $url, array $events, ?string $description = null): WebhookCreation
    {
        $creation = new WebhookCreation();
        $creation->setId('wb_'.bin2hex(random_bytes(4)));
        $creation->setEvents($events);
        $creation->setDescription($description);
        $creation->setSecret('wb_'.bin2hex(random_bytes(34)));

        return $creation;
    }

    public function deregisterWebhook(string $id): void
    {
        // TODO: Implement deregisterWebhook() method.
    }
}

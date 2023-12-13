<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    public function registerWebhook(string $url, array $events, string $description = null): WebhookCreation
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

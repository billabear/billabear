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

namespace App\Dto\Response\App\Developer\Webhook;

class ViewWebhookEvent
{
    protected WebhookEvent $event;

    protected array $responses;

    public function getEvent(): WebhookEvent
    {
        return $this->event;
    }

    public function setEvent(WebhookEvent $event): void
    {
        $this->event = $event;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function setResponses(array $responses): void
    {
        $this->responses = $responses;
    }
}
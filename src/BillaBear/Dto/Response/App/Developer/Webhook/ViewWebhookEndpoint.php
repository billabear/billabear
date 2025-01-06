<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Developer\Webhook;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewWebhookEndpoint
{
    #[SerializedName('webhook_endpoint')]
    protected WebhookEndpoint $webhookEndpoint;

    public function getWebhookEndpoint(): WebhookEndpoint
    {
        return $this->webhookEndpoint;
    }

    public function setWebhookEndpoint(WebhookEndpoint $webhookEndpoint): void
    {
        $this->webhookEndpoint = $webhookEndpoint;
    }
}

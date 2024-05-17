<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Integrations\Slack;

class CreateSlackNotificationView
{
    private array $webhooks = [];

    public function getWebhooks(): array
    {
        return $this->webhooks;
    }

    public function setWebhooks(array $webhooks): void
    {
        $this->webhooks = $webhooks;
    }
}

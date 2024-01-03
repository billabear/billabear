<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Webhook;

use App\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Config\WebhookConfig;

class ConfigFactory
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function createConfig(): WebhookConfig
    {
        $secret = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getWebhookSecret();
        if (is_null($secret)) {
            throw new \Exception('Webhook secret is not set');
        }

        return new WebhookConfig($secret);
    }
}

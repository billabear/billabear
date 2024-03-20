<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

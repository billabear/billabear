<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook;

use BillaBear\Repository\SettingsRepositoryInterface;
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

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\UpdateChecker;

use App\Kernel;
use App\Repository\SettingsRepositoryInterface;
use Http\Discovery\Psr18ClientDiscovery;
use Nyholm\Psr7\Request;

class UpdateChecker
{
    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function execute(): void
    {
        $payload = [];
        $settings = $this->settingsRepository->getDefaultSettings();
        $payload['url'] = $settings->getSystemSettings()->getSystemUrl();
        $payload['id'] = $settings->getId();
        $payload['version'] = Kernel::VERSION;

        $request = new Request('POST', 'https://announce.billabear.com/update', headers: ['Content-Type' => 'application/json'], body: json_encode($payload));

        $client = Psr18ClientDiscovery::find();
        $response = $client->sendRequest($request);
        $data = json_decode($response->getBody()->getContents(), true);

        if (str_contains('-dev', Kernel::VERSION)) {
            return;
        }

        if (version_compare(Kernel::VERSION, $data['version'], '<')) {
            $settings->getSystemSettings()->setUpdateAvailable(true);
        }

        $this->settingsRepository->save($settings);
    }
}

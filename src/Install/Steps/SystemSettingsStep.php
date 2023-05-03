<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Install\Steps;

use App\Entity\Settings;
use App\Install\Dto\InstallRequest;
use App\Repository\SettingsRepositoryInterface;

class SystemSettingsStep
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function install(InstallRequest $request): void
    {
        $systemSettings = new Settings\SystemSettings();
        $systemSettings->setTimezone($request->getTimezone());
        $systemSettings->setWebhookUrl($request->getWebhookUrl());

        $notification = new Settings\NotificationSettings();
        $notification->setEmsp(Settings\NotificationSettings::EMSP_SYSTEM);
        $notification->setDefaultOutgoingEmail($request->getFromEmail());

        $settings = new Settings();
        $settings->setTag('default');
        $settings->setSystemSettings($systemSettings);
        $settings->setNotificationSettings($notification);

        $this->settingsRepository->save($settings);
    }
}

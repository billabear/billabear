<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Steps;

use BillaBear\Entity\Settings;
use BillaBear\Install\Dto\InstallRequest;
use BillaBear\Repository\SettingsRepositoryInterface;

class SystemSettingsStep
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function install(InstallRequest $request): void
    {
        $systemSettings = new Settings\SystemSettings();
        $systemSettings->setTimezone($request->getTimezone());
        $systemSettings->setSystemUrl($request->getWebhookUrl());
        $systemSettings->setMainCurrency(strtoupper($request->getCurrency()));
        $systemSettings->setInvoiceNumberGeneration('random');
        $systemSettings->setUseStripeBilling(false);
        $systemSettings->setDefaultInvoiceDueTime('30 days');

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

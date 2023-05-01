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

namespace App\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class NotificationSettingsView
{
    #[SerializedName('notification_settings')]
    private NotificationSettings $notificationSettings;

    #[SerializedName('emsp_choices')]
    private array $emspChoices;

    public function getNotificationSettings(): NotificationSettings
    {
        return $this->notificationSettings;
    }

    public function setNotificationSettings(NotificationSettings $notificationSettings): void
    {
        $this->notificationSettings = $notificationSettings;
    }

    public function getEmspChoices(): array
    {
        return $this->emspChoices;
    }

    public function setEmspChoices(array $emspChoices): void
    {
        $this->emspChoices = $emspChoices;
    }
}

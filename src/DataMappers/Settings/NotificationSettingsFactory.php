<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Settings;

use App\Dto\Request\App\Settings\NotificationSettings as RequestDto;
use App\Dto\Response\App\Settings\NotificationSettings as AppDto;
use App\Entity\Settings\NotificationSettings;

class NotificationSettingsFactory
{
    public function updateEntity(RequestDto $dto, NotificationSettings $settings): NotificationSettings
    {
        $settings->setEmsp($dto->getEmsp());
        $settings->setSendCustomerNotifications($dto->getSendCustomerNotifications());
        $settings->setDefaultOutgoingEmail($dto->getDefaultOutgoingEmail());
        $settings->setEmspApiKey($dto->getEmspApiKey());
        $settings->setEmspApiUrl($dto->getEmspApiUrl());
        $settings->setEmspDomain($dto->getEmspDomain());

        return $settings;
    }

    public function createAppDto(NotificationSettings $settings): AppDto
    {
        $dto = new AppDto();
        $dto->setEmsp($settings->getEmsp());
        $dto->setEmspApiKey($settings->getEmspApiKey());
        $dto->setEmspApiUrl($settings->getEmspApiUrl());
        $dto->setEmspDomain($settings->getEmspDomain());
        $dto->setDefaultOutgoingEmail($settings->getDefaultOutgoingEmail());
        $dto->setSendCustomerNotifications($settings->getSendCustomerNotifications());

        return $dto;
    }
}

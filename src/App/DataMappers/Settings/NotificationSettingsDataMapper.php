<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Settings;

use App\Dto\Request\App\Settings\NotificationSettings as RequestDto;
use App\Dto\Response\App\Settings\NotificationSettings as AppDto;
use App\Entity\Settings\NotificationSettings;

class NotificationSettingsDataMapper
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

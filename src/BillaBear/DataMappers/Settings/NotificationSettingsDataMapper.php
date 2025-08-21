<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Settings;

use BillaBear\Dto\Request\App\Settings\NotificationSettings as RequestDto;
use BillaBear\Dto\Response\App\Settings\NotificationSettings as AppDto;
use BillaBear\Entity\Settings\NotificationSettings;

class NotificationSettingsDataMapper
{
    public function updateEntity(RequestDto $dto, NotificationSettings $settings): NotificationSettings
    {
        $settings->setEmsp($dto->emsp);
        $settings->setSendCustomerNotifications($dto->sendCustomerNotifications);
        $settings->setDefaultOutgoingEmail($dto->defaultOutgoingEmail);
        $settings->setEmspApiKey($dto->emspApiKey);
        $settings->setEmspApiUrl($dto->emspApiUrl);
        $settings->setEmspDomain($dto->emspDomain);

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

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

namespace App\Controller\App\Settings;

use App\Factory\NotificationSettingsFactory;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationSettingsController
{
    #[Route('/app/settings/notification-settings', name: 'app_app_settings_notificationsettings_readsettings', methods: ['GET'])]
    public function readSettings(
        SettingsRepository $settingsRepository,
        NotificationSettingsFactory $factory,
        SerializerInterface $serializer,
    ): Response {
        $settings = $settingsRepository->getDefaultSettings();
        $dto = $factory->createAppDto($settings->getNotificationSettings());
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\DataMappers\Settings\NotificationSettingsDataMapper;
use BillaBear\Dto\Request\App\Settings\NotificationSettings;
use BillaBear\Dto\Response\App\Settings\NotificationSettingsView;
use BillaBear\Repository\SettingsRepository;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_DEVELOPER')]
class NotificationSettingsController
{
    use LoggerAwareTrait;

    #[Route('/app/settings/notification-settings', name: 'app_app_settings_notificationsettings_readsettings', methods: ['GET'])]
    public function readSettings(
        SettingsRepository $settingsRepository,
        NotificationSettingsDataMapper $factory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to read notification settings');

        $settings = $settingsRepository->getDefaultSettings();
        $notificationSettings = $factory->createAppDto($settings->getNotificationSettings());

        $dto = new NotificationSettingsView();
        $dto->setNotificationSettings($notificationSettings);
        $dto->setEmspChoices(\BillaBear\Entity\Settings\NotificationSettings::EMSP_CHOICES);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/notification-settings', name: 'app_app_settings_notificationsettings_updatesettings', methods: ['POST'])]
    public function updateSettings(
        Request $request,
        SettingsRepository $settingsRepository,
        NotificationSettingsDataMapper $factory,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to update notification settings');
        $requestDto = $serializer->deserialize($request->getContent(), NotificationSettings::class, 'json');
        $errors = $validator->validate($requestDto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $settings = $settingsRepository->getDefaultSettings();
        $notificationSettings = $factory->updateEntity($requestDto, $settings->getNotificationSettings());
        $settings->setNotificationSettings($notificationSettings);
        $settingsRepository->save($settings);
        $dto = $factory->createAppDto($settings->getNotificationSettings());
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Settings;

use App\DataMappers\Settings\SystemSettingsDataMapper;
use App\Dto\Request\App\Settings\SystemSettings;
use App\Dto\Response\App\Settings\SystemSettingsView;
use App\Repository\SettingsRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_DEVELOPER')]
class SystemSettingsController
{
    #[Route('/app/settings/system', name: 'app_app_settings_systemsettings_readsettings', methods: ['GET'])]
    public function readSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
        SystemSettingsDataMapper $systemSettingsFactory,
    ): Response {
        $systemSettingsDto = $systemSettingsFactory->createAppDto($settingsRepository->getDefaultSettings()->getSystemSettings());
        $dto = new SystemSettingsView();
        $dto->setSystemSettings($systemSettingsDto);
        $dto->setTimezones(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL));

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/system', name: 'app_app_settings_systemsettings_updatesettings', methods: ['POST'])]
    public function updateSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        SystemSettingsDataMapper $factory,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $requestDto = $serializer->deserialize($request->getContent(), SystemSettings::class, 'json');
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
        $systemSettings = $factory->updateEntity($requestDto, $settings->getSystemSettings());
        $settings->setSystemSettings($systemSettings);
        $settingsRepository->save($settings);
        $dto = $factory->createAppDto($settings->getSystemSettings());
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/update/dismiss', methods: ['POST'])]
    public function dismissUpdate(
        SettingsRepositoryInterface $repository,
    ) {
        $settings = $repository->getDefaultSettings();
        $settings->getSystemSettings()->setUpdateAvailableDismissed(true);
        $repository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}

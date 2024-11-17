<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\DataMappers\Settings\SystemSettingsDataMapper;
use BillaBear\Dto\Request\App\Settings\SystemSettings;
use BillaBear\Dto\Response\App\Settings\SystemSettingsView;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_DEVELOPER')]
class SystemSettingsController
{
    use LoggerAwareTrait;

    #[Route('/app/settings/system', name: 'app_app_settings_systemsettings_readsettings', methods: ['GET'])]
    public function readSettings(
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
        SystemSettingsDataMapper $systemSettingsFactory,
    ): Response {
        $this->getLogger()->info('Received request to read settings');

        $systemSettingsDto = $systemSettingsFactory->createAppDto($settingsRepository->getDefaultSettings()->getSystemSettings());
        $dto = new SystemSettingsView();
        $dto->setSystemSettings($systemSettingsDto);
        $dto->setTimezones(\DateTimeZone::listIdentifiers());

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
        $this->getLogger()->info('Received request to update settings');
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
        $this->getLogger()->info('Request to dismiss that an update is available');
        $settings = $repository->getDefaultSettings();
        $settings->getSystemSettings()->setUpdateAvailableDismissed(true);
        $repository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}

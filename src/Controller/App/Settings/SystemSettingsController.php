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

use App\Dto\Request\App\Settings\SystemSettings;
use App\Factory\SystemSettingsFactory;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SystemSettingsController
{
    #[Route('/app/settings/system', name: 'app_app_settings_systemsettings_updatesettings', methods: ['POST'])]
    public function updateSettings(
        Request $request,
        SettingsRepository $settingsRepository,
        SystemSettingsFactory $factory,
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
}

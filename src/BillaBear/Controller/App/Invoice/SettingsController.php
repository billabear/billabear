<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Invoice;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Invoice\SettingsDataMapper;
use BillaBear\Dto\Request\App\Invoice\UpdateSettings;
use BillaBear\Dto\Response\App\Invoice\ViewSettings;
use BillaBear\Repository\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class SettingsController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/invoice/settings', methods: ['GET'])]
    public function readSettings(
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
        SettingsDataMapper $systemSettingsFactory,
    ): Response {
        $this->getLogger()->info('Received request to read invoice settings');

        $systemSettingsDto = $systemSettingsFactory->createAppDto($settingsRepository->getDefaultSettings()->getSystemSettings());
        $dto = new ViewSettings();
        $dto->setInvoiceSettings($systemSettingsDto);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/invoice/settings', methods: ['POST'])]
    public function updateSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        SettingsDataMapper $factory,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to update invoice settings');
        $requestDto = $serializer->deserialize($request->getContent(), UpdateSettings::class, 'json');
        $errors = $validator->validate($requestDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $settings = $settingsRepository->getDefaultSettings();
        $systemSettings = $factory->updateInvoiceSettings($requestDto, $settings->getSystemSettings());
        $settings->setSystemSettings($systemSettings);
        $settingsRepository->save($settings);
        $dto = $factory->createAppDto($settings->getSystemSettings());
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}

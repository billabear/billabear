<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Settings\TaxSettingsDataMapper;
use BillaBear\Dto\Request\App\Settings\Tax\TaxSettings;
use BillaBear\Dto\Request\App\Settings\Tax\VatSense;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ADMIN')]
class TaxSettingsController
{
    use LoggerAwareTrait;
    use ValidationErrorResponseTrait;

    #[Route('/app/settings/tax', name: 'app_app_settings_taxsettings_readsettings', methods: ['GET'])]
    public function readSettings(
        SerializerInterface $serializer,
        TaxSettingsDataMapper $taxSettingsFactory,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->getLogger()->info('Received request to read tax settings');

        $settings = $settingsRepository->getDefaultSettings();
        $taxSettings = $settings->getTaxSettings();
        $outputDto = $taxSettingsFactory->createAppDto($taxSettings);
        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/tax', name: 'app_app_settings_taxsettings_setsettings', methods: ['POST'])]
    public function setSettings(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        TaxSettingsDataMapper $taxSettingsFactory,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->getLogger()->info('Received request to set tax settings');

        /** @var TaxSettings $dto */
        $dto = $serializer->deserialize($request->getContent(), TaxSettings::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        $settings = $settingsRepository->getDefaultSettings();
        $taxSettings = $taxSettingsFactory->updateTaxSettings($dto, $settings->getTaxSettings());
        $settings->setTaxSettings($taxSettings);
        $settingsRepository->save($settings);

        $outputDto = $taxSettingsFactory->createAppDto($taxSettings);
        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/tax/vatsense', name: 'app_app_settings_taxsettings_vat_setsettings', methods: ['POST'])]
    public function setVatSenseSettings(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        TaxSettingsDataMapper $taxSettingsFactory,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->getLogger()->info('Received request to set tax settings');

        /** @var VatSense $dto */
        $dto = $serializer->deserialize($request->getContent(), VatSense::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        $settings = $settingsRepository->getDefaultSettings();
        $taxSettings = $taxSettingsFactory->updateVatSense($dto, $settings->getTaxSettings());
        $settings->setTaxSettings($taxSettings);
        $settingsRepository->save($settings);

        $outputDto = $taxSettingsFactory->createAppDto($taxSettings);
        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }
}

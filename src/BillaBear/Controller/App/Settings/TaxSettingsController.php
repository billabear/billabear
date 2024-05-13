<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\DataMappers\Settings\TaxSettingsDataMapper;
use BillaBear\Dto\Request\BillaBear\Settings\Tax\TaxSettings;
use BillaBear\Repository\SettingsRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted('ROLE_ADMIN')]
class TaxSettingsController
{
    #[Route('/app/settings/tax', name: 'app_app_settings_taxsettings_readsettings', methods: ['GET'])]
    public function readSettings(
        SerializerInterface $serializer,
        TaxSettingsDataMapper $taxSettingsFactory,
        SettingsRepositoryInterface $settingsRepository,
    ) {
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
        TaxSettingsDataMapper $taxSettingsFactory,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        /** @var TaxSettings $dto */
        $dto = $serializer->deserialize($request->getContent(), TaxSettings::class, 'json');
        $taxSettings = $taxSettingsFactory->createEntity($dto);
        $settings = $settingsRepository->getDefaultSettings();
        $settings->setTaxSettings($taxSettings);
        $settingsRepository->save($settings);

        $outputDto = $taxSettingsFactory->createAppDto($taxSettings);
        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }
}

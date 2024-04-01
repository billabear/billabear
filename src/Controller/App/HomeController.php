<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\Entity\Customer;
use App\Exception\NoRateForCountryException;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use App\Tax\CountryRules;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
{
    #[Route('/app/system/data', name: 'app_system_data', methods: ['GET'])]
    public function getSystemData(
        SettingsRepositoryInterface $repository,
        CountryRules $taxRateProvider,
        BrandSettingsRepositoryInterface $brandSettingsRepository,
    ): Response {
        $defaultBrand = $brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);

        try {
            $taxRateProvider->getDigitalVatPercentage($defaultBrand->getAddress());
            $hasDefaultTax = true;
        } catch (NoRateForCountryException $exception) {
            $hasDefaultTax = $defaultBrand->hasTaxRate();
        }

        $json = [
            'has_stripe_import' => $repository->getDefaultSettings()->getOnboardingSettings()->isHasStripeImports(),
            'is_update_available' => $repository->getDefaultSettings()->getSystemSettings()->isUpdateAvailable() && !$repository->getDefaultSettings()->getSystemSettings()->getUpdateAvailableDismissed(),
            'has_default_tax' => $hasDefaultTax,
        ];

        return new JsonResponse($json);
    }
}

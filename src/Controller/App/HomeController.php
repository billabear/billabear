<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

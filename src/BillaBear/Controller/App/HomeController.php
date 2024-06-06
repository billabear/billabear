<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Entity\Customer;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController
{
    use LoggerAwareTrait;

    #[Route('/app/system/data', name: 'app_system_data', methods: ['GET'])]
    public function getSystemData(
        SettingsRepositoryInterface $repository,
        BrandSettingsRepositoryInterface $brandSettingsRepository,
    ): Response {
        $this->getLogger()->info('Received request for system data');

        $defaultBrand = $brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);

        $json = [
            'has_stripe_import' => $repository->getDefaultSettings()->getOnboardingSettings()->isHasStripeImports(),
            'is_update_available' => $repository->getDefaultSettings()->getSystemSettings()->isUpdateAvailable() && !$repository->getDefaultSettings()->getSystemSettings()->getUpdateAvailableDismissed(),
            'has_default_tax' => true,
        ];

        return new JsonResponse($json);
    }
}

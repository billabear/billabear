<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Entity\Customer;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\SubscriptionPlanRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SystemController
{
    use LoggerAwareTrait;

    #[Route('/app/system/data', name: 'app_system_data', methods: ['GET'])]
    public function getSystemData(
        SettingsRepositoryInterface $repository,
        BrandSettingsRepositoryInterface $brandSettingsRepository,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        #[Autowire(env: 'STRIPE_PRIVATE_API_KEY')]
        string $privateApiKey,
    ): Response {
        $this->getLogger()->info('Received request for system data');

        $defaultBrand = $brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);
        $hasKey = false;
        if (empty($privateApiKey)) {
            $key = $repository->getDefaultSettings()->getSystemSettings()->getStripePrivateKey();
            if (!empty($key)) {
                $hasKey = true;
            }
        } else {
            $hasKey = true;
        }
        $productResult = $productRepository->getList([], limit: 1);
        $hasProduct = count($productResult->getResults()) > 0;

        $customerResult = $customerRepository->getList([], limit: 1);
        $hasCustomer = count($customerResult->getResults()) > 0;

        $subscriptionResult = $subscriptionRepository->getList([], limit: 1);
        $hasSubscription = count($subscriptionResult->getResults()) > 0;

        $subscriptionPlanResult = $subscriptionPlanRepository->getList([], limit: 1);
        $hasSubscriptionPlan = count($subscriptionPlanResult->getResults()) > 0;

        $json = [
            'has_stripe_key' => $hasKey,
            'has_product' => $hasProduct,
            'has_customer' => $hasCustomer,
            'has_subscription' => $hasSubscription,
            'has_subscription_plan' => $hasSubscriptionPlan,
            'has_stripe_imports' => $repository->getDefaultSettings()->getOnboardingSettings()->isHasStripeImports(),
            'is_update_available' => $repository->getDefaultSettings()->getSystemSettings()->isUpdateAvailable() && !$repository->getDefaultSettings()->getSystemSettings()->getUpdateAvailableDismissed(),
            'has_default_tax' => true,
        ];

        return new JsonResponse($json);
    }
}

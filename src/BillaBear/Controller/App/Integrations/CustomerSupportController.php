<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\DataMappers\Integrations\IntegrationDataMapper;
use BillaBear\Dto\Response\App\Integrations\AccountingIntegrationView;
use BillaBear\Integrations\CustomerSupport\Messenger\EnableIntegration;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerSupportController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/integrations/customer-support/settings', name: 'customer_support_settings', methods: ['GET'])]
    public function readAccountingSettings(
        IntegrationManager $integrationManager,
        IntegrationDataMapper $integrationDataMapper,
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Reading customer support integration settings');

        $settings = $settingsRepository->getDefaultSettings();
        $integrations = $integrationManager->getCustomerSupportIntegrations();
        $integrationDtos = array_map([$integrationDataMapper, 'createAppDto'], $integrations);

        $viewDto = new AccountingIntegrationView(
            $integrationDtos,
            $settings->getCustomerSupportIntegration()->getEnabled(),
            $settings->getCustomerSupportIntegration()->getIntegration(),
            $settings->getCustomerSupportIntegration()->getSettings(),
        );
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/integrations/customer-support/settings', name: 'customer_support_settings_write', methods: ['POST'])]
    public function writeAccountingSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        MessageBusInterface $messageBus,
    ): Response {
        $this->getLogger()->info('Writing customer support integration settings');
        $data = json_decode($request->getContent(), true);
        $settings = $settingsRepository->getDefaultSettings();

        $currentEnable = $settings->getCustomerSupportIntegration()->getEnabled();
        $currentIntegration = $settings->getCustomerSupportIntegration()->getIntegration();

        $settings->getCustomerSupportIntegration()->setEnabled($data['enabled']);
        $settings->getCustomerSupportIntegration()->setIntegration($data['integration_name']);
        $settings->getCustomerSupportIntegration()->setSettings($data['settings']);
        $settingsRepository->save($settings);

        $newIntegration = $settings->getCustomerSupportIntegration()->getIntegration() !== $currentIntegration;

        if ((false === $currentEnable || $newIntegration) && $settings->getCustomerSupportIntegration()->getEnabled()) {
            $messageBus->dispatch(new EnableIntegration($newIntegration));
        }

        return new JsonResponse(['settings' => $data['settings']]);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}

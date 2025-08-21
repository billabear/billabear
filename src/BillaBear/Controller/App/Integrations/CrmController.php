<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\DataMappers\Integrations\IntegrationDataMapper;
use BillaBear\Dto\Response\App\Integrations\AccountingIntegrationView;
use BillaBear\Integrations\Accounting\Messenger\DisableIntegration;
use BillaBear\Integrations\Accounting\Messenger\EnableIntegration;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CrmController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/integrations/crm/settings', name: 'crm_settings', methods: ['GET'])]
    public function readAccountingSettings(
        IntegrationManager $integrationManager,
        IntegrationDataMapper $integrationDataMapper,
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Reading CRM integration settings');

        $settings = $settingsRepository->getDefaultSettings();
        $integrations = $integrationManager->getCrmIntegrations();
        $integrationDtos = array_map([$integrationDataMapper, 'createAppDto'], $integrations);

        $viewDto = new AccountingIntegrationView(
            $integrationDtos,
            $settings->getCrmIntegration()->getEnabled(),
            $settings->getCrmIntegration()->getIntegration(),
            $settings->getCrmIntegration()->getSettings(),
        );
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/integrations/crm/settings', name: 'crm_settings_write', methods: ['POST'])]
    public function writeAccountingSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        MessageBusInterface $messageBus,
    ): Response {
        $this->getLogger()->info('Writing CRM integration settings');
        $data = json_decode($request->getContent(), true);
        $settings = $settingsRepository->getDefaultSettings();

        $currentEnable = $settings->getCrmIntegration()->getEnabled();
        $currentIntegration = $settings->getCrmIntegration()->getIntegration();

        $settings->getCrmIntegration()->setEnabled($data['enabled']);
        $settings->getCrmIntegration()->setIntegration($data['integration_name']);
        $settings->getCrmIntegration()->setSettings($data['settings']);
        $settingsRepository->save($settings);
        $newIntegration = $settings->getCrmIntegration()->getIntegration() !== $currentIntegration;

        if ((false === $currentEnable || $newIntegration) && $settings->getCrmIntegration()->getEnabled()) {
            $this->getLogger()->info('Enabling new CRM integration', ['old_integration' => $currentIntegration, 'new_integration' => $settings->getCrmIntegration()->getIntegration()]);
            $messageBus->dispatch(new EnableIntegration($newIntegration));
        }

        return new JsonResponse(['settings' => $data['settings']]);
    }

    #[Route('/app/integrations/crm/disconnect', name: 'crm_settings_disconnect', methods: ['POST'])]
    public function disconnect(
        MessageBusInterface $messageBus,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Disconnecting accounting integration');
        $settings = $settingsRepository->getDefaultSettings();
        $settings->getCrmIntegration()->setEnabled(false);
        $settings->getCrmIntegration()->getOauthSettings()->setAccessToken(null);
        $settings->getCrmIntegration()->getOauthSettings()->setExpiresAt(new \DateTime('now'));
        $settingsRepository->save($settings);

        $messageBus->dispatch(new DisableIntegration());

        return new JsonResponse([]);
    }

    #[Route('/app/integrations/crm/disable', name: 'accounting_settings_disable', methods: ['POST'])]
    public function disable(
        MessageBusInterface $messageBus,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Enabling accounting integration');
        $settings = $settingsRepository->getDefaultSettings();
        $settings->getCrmIntegration()->setEnabled(false);
        $settingsRepository->save($settings);

        $messageBus->dispatch(new DisableIntegration());

        return new JsonResponse([]);
    }

    #[Route('/app/integrations/accounting/enable', name: 'accounting_settings_enable', methods: ['POST'])]
    public function enable()
    {
        $this->getLogger()->info('Enabling accounting integration');
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}

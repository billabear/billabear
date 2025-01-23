<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
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
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AccountingController
{
    use LoggerAwareTrait;

    #[Route('/app/integrations/accounting/settings', name: 'accounting_settings', methods: ['GET'])]
    public function readAccountingSettings(
        IntegrationManager $integrationManager,
        IntegrationDataMapper $integrationDataMapper,
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Reading accounting integration settings');

        $settings = $settingsRepository->getDefaultSettings();
        $integrations = $integrationManager->getAccountingIntegrations();
        $integrationDtos = array_map([$integrationDataMapper, 'createAppDto'], $integrations);

        $viewDto = new AccountingIntegrationView(
            $integrationDtos,
            $settings->getAccountingIntegration()->getEnabled(),
            $settings->getAccountingIntegration()->getIntegration(),
            $settings->getAccountingIntegration()->getSettings(),
        );
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/integrations/accounting/settings', name: 'accounting_settings_write', methods: ['POST'])]
    public function writeAccountingSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        MessageBusInterface $messageBus,
    ): Response {
        $this->getLogger()->info('Writing accounting integration settings');
        $data = json_decode($request->getContent(), true);
        $settings = $settingsRepository->getDefaultSettings();

        $currentEnable = $settings->getAccountingIntegration()->getEnabled();
        $currentIntegration = $settings->getAccountingIntegration()->getIntegration();

        $settings->getAccountingIntegration()->setEnabled($data['enabled']);
        $settings->getAccountingIntegration()->setIntegration($data['integration_name']);
        $settings->getAccountingIntegration()->setSettings($data['settings']);
        $settingsRepository->save($settings);
        $newIntegration = $settings->getAccountingIntegration()->getIntegration() !== $currentIntegration;

        if ((false === $currentEnable || $newIntegration) && $settings->getAccountingIntegration()->getEnabled()) {
            $messageBus->dispatch(new EnableIntegration($newIntegration));
        }

        return new JsonResponse(['settings' => $data['settings']]);
    }

    #[Route('/app/integrations/accounting/disable', name: 'accounting_settings_disable', methods: ['POST'])]
    public function disconnect()
    {
        $this->getLogger()->info('Disconnecting accounting integration');
    }

    #[Route('/app/integrations/accounting/disable', name: 'accounting_settings_disable', methods: ['POST'])]
    public function disable(
        MessageBusInterface $messageBus,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Enabling accounting integration');
        $settings = $settingsRepository->getDefaultSettings();
        $settings->getAccountingIntegration()->setEnabled(false);
        $settings->getAccountingIntegration()->setUpdatedAt(new \DateTime());
        $settingsRepository->save($settings);

        $messageBus->dispatch(new DisableIntegration());

        return new JsonResponse([]);
    }

    #[Route('/app/integrations/accounting/enable', name: 'accounting_settings_enable', methods: ['POST'])]
    public function enable()
    {
        $this->getLogger()->info('Enabling accounting integration');
    }
}

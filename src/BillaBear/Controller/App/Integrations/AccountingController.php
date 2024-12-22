<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\DataMappers\Integrations\IntegrationDataMapper;
use BillaBear\Dto\Response\App\Integrations\AccountingIntegrationView;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Integrations\Messenger\Accounting\DisableIntegration;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AccountingController
{
    use LoggerAwareTrait;

    #[Route('/app/integrations/accounting/settings', name: 'accounting_settings')]
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
            $settings->getAccountingIntegration()->getApiKey(),
        );
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
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

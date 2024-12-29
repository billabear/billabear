<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\DataMappers\Integrations\IntegrationDataMapper;
use BillaBear\Dto\Response\App\Integrations\AccountingIntegrationView;
use BillaBear\Integrations\CustomerSupport\Messenger\EnableIntegration;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class NewsletterController
{
    use LoggerAwareTrait;

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/integrations/newsletter/settings', name: 'newsletter_settings', methods: ['GET'])]
    public function readAccountingSettings(
        IntegrationManager $integrationManager,
        IntegrationDataMapper $integrationDataMapper,
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Reading newsletter integration settings');

        $settings = $settingsRepository->getDefaultSettings();
        $integrations = $integrationManager->getNewsletterIntegrations();
        $integrationDtos = array_map([$integrationDataMapper, 'createAppDto'], $integrations);

        $viewDto = new AccountingIntegrationView(
            $integrationDtos,
            $settings->getNewsletterIntegration()->getEnabled(),
            $settings->getNewsletterIntegration()->getIntegration(),
            $settings->getNewsletterIntegration()->getSettings(),
        );
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/integrations/newsletter/settings', name: 'newsletter_settings_write', methods: ['POST'])]
    public function writeAccountingSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        MessageBusInterface $messageBus,
    ): Response {
        $this->getLogger()->info('Writing newsletter integration settings');
        $data = json_decode($request->getContent(), true);
        $settings = $settingsRepository->getDefaultSettings();

        $currentEnable = $settings->getNewsletterIntegration()->getEnabled();
        $currentIntegration = $settings->getNewsletterIntegration()->getIntegration();

        $settings->getNewsletterIntegration()->setEnabled($data['enabled']);
        $settings->getNewsletterIntegration()->setIntegration($data['integration_name']);
        $settings->getNewsletterIntegration()->setSettings($data['settings']);
        $settingsRepository->save($settings);

        $newIntegration = $settings->getNewsletterIntegration()->getIntegration() !== $currentIntegration;

        if ((false === $currentEnable || $newIntegration) && $settings->getNewsletterIntegration()->getEnabled()) {
            $messageBus->dispatch(new EnableIntegration($newIntegration));
        }

        return new JsonResponse(['settings' => $data['settings']]);
    }
}

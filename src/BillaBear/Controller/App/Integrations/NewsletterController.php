<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\DataMappers\Integrations\IntegrationDataMapper;
use BillaBear\DataMappers\Integrations\NewsletterListsDataMapper;
use BillaBear\Dto\Response\App\Integrations\NewsletterIntegrationView;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Integrations\Newsletter\Messenger\EnableIntegration;
use BillaBear\Repository\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class NewsletterController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/integrations/newsletter/settings', name: 'newsletter_settings', methods: ['GET'])]
    public function readNewsletterSettings(
        IntegrationManager $integrationManager,
        IntegrationDataMapper $integrationDataMapper,
        NewsletterListsDataMapper $newsletterListsDataMapper,
        SettingsRepositoryInterface $settingsRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Reading newsletter integration settings');

        $settings = $settingsRepository->getDefaultSettings();
        $json = $this->buildView($integrationManager, $integrationDataMapper, $settings, $newsletterListsDataMapper, $serializer);

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/integrations/newsletter/settings', name: 'newsletter_settings_write', methods: ['POST'])]
    public function writeNewsletterSettings(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
        IntegrationManager $integrationManager,
        IntegrationDataMapper $integrationDataMapper,
        NewsletterListsDataMapper $newsletterListsDataMapper,
        MessageBusInterface $messageBus,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Writing newsletter integration settings');
        $data = json_decode($request->getContent(), true);
        $settings = $settingsRepository->getDefaultSettings();

        $currentEnable = $settings->getNewsletterIntegration()->getEnabled();
        $currentIntegration = $settings->getNewsletterIntegration()->getIntegration();

        $settings->getNewsletterIntegration()->setEnabled($data['enabled']);
        $settings->getNewsletterIntegration()->setIntegration($data['integration_name']);
        $settings->getNewsletterIntegration()->setSettings($data['settings']);
        $settings->getNewsletterIntegration()->setMarketingListId($data['marketing_list_id']);
        $settings->getNewsletterIntegration()->setAnnouncementListId($data['announcement_list_id']);
        $settingsRepository->save($settings);

        $newIntegration = $settings->getNewsletterIntegration()->getIntegration() !== $currentIntegration;

        if ((false === $currentEnable || $newIntegration) && $settings->getNewsletterIntegration()->getEnabled()) {
            $messageBus->dispatch(new EnableIntegration($newIntegration));
        }

        $json = $this->buildView($integrationManager, $integrationDataMapper, $settings, $newsletterListsDataMapper, $serializer);

        return new JsonResponse($json, json: true);
    }

    public function buildView(
        IntegrationManager $integrationManager,
        IntegrationDataMapper $integrationDataMapper,
        \BillaBear\Entity\Settings $settings,
        NewsletterListsDataMapper $newsletterListsDataMapper,
        SerializerInterface $serializer,
    ): string {
        $integrations = $integrationManager->getNewsletterIntegrations();
        $integrationDtos = array_map([$integrationDataMapper, 'createAppDto'], $integrations);
        $lists = [];
        if (null !== $settings->getNewsletterIntegration()->getIntegration()) {
            $integration = $integrationManager->getNewsletterIntegration($settings->getNewsletterIntegration()->getIntegration());
            $lists = $integration->getListService()->getLists();
        }

        $listDtos = array_map([$newsletterListsDataMapper, 'createAppDto'], $lists);
        $viewDto = new NewsletterIntegrationView(
            $integrationDtos,
            $settings->getNewsletterIntegration()->getEnabled(),
            $settings->getNewsletterIntegration()->getIntegration(),
            $settings->getNewsletterIntegration()->getSettings(),
            $listDtos,
            $settings->getNewsletterIntegration()->getMarketingListId(),
            $settings->getNewsletterIntegration()->getAnnouncementListId(),
        );
        $json = $serializer->serialize($viewDto, 'json');

        return $json;
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}

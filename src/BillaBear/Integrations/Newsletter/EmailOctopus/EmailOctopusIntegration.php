<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\EmailOctopus;

use BillaBear\Exception\Integrations\MissingConfigurationException;
use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\Newsletter\CustomerServiceInterface;
use BillaBear\Integrations\Newsletter\ListServiceInterface;
use BillaBear\Integrations\Newsletter\NewsletterIntegrationInterface;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;
use GoranPopovic\EmailOctopus\Client;
use GoranPopovic\EmailOctopus\EmailOctopus;
use Parthenon\Common\LoggerAwareTrait;

class EmailOctopusIntegration implements IntegrationInterface, NewsletterIntegrationInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function setup(): void
    {
    }

    public function getType(): IntegrationType
    {
        return IntegrationType::NEWSLETTER;
    }

    public function getName(): string
    {
        return 'emailoctopus';
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::API_KEY;
    }

    public function getOauthConfig(): OauthConfig
    {
        throw new UnsupportedFeatureException('Oauth is not supported for this integration');
    }

    public function getSettings(): array
    {
        return [
            [
                'name' => 'api_key',
                'label' => 'app.integrations.general.fields.api_key',
                'type' => 'text',
                'required' => true,
            ],
        ];
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        $customerService = new CustomerService($this->createClient());
        $customerService->setLogger($this->getLogger());

        return $customerService;
    }

    public function getListService(): ListServiceInterface
    {
        $listService = new ListService($this->createClient());
        $listService->setLogger($this->getLogger());

        return $listService;
    }

    public function getAccountId(): string
    {
        throw new UnsupportedFeatureException('EmailOctopus does not support account id');
    }

    private function createClient(): Client
    {
        $settings = $this->settingsRepository->getDefaultSettings()->getNewsletterIntegration()->getSettings();

        if (!isset($settings['api_key'])) {
            throw new MissingConfigurationException('Email Octopus API Key is required. Please set it in your .env file.');
        }

        return EmailOctopus::client($settings['api_key']);
    }
}

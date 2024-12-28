<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Zendesk;

use BillaBear\Exception\Integrations\MissingConfigurationException;
use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\CustomerSupport\CustomerServiceInterface;
use BillaBear\Integrations\CustomerSupport\CustomerSupportIntegrationInterface;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;
use Zendesk\API\HttpClient as ZendeskAPI;

class ZendeskIntegration implements IntegrationInterface, CustomerSupportIntegrationInterface
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function setup(): void
    {
        // TODO: Implement setup() method.
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        // TODO: Implement getCustomerService() method.
    }

    public function getType(): IntegrationType
    {
        return IntegrationType::CUSTOMER_SUPPORT;
    }

    public function getName(): string
    {
        return 'zendesk';
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::API_KEY;
    }

    public function getOauthConfig(): OauthConfig
    {
        throw new UnsupportedFeatureException('Zendesk does not support OAuth');
    }

    public function getSettings(): array
    {
        return [
            [
                'name' => 'token',
                'label' => 'app.customer_support.integration.zendesk.token',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'subdomain',
                'label' => 'app.customer_support.integration.zendesk.subdomain',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'username',
                'label' => 'app.customer_support.integration.zendesk.username',
                'type' => 'text',
                'required' => true,
            ],
        ];
    }

    private function buildZendeskClient(): ZendeskAPI
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        $zendeskSettings = $settings->getCustomerSupportIntegration()->getSettings();

        if (!isset($zendeskSettings['token']) || !isset($zendeskSettings['subdomain']) || !isset($zendeskSettings['username'])) {
            throw new MissingConfigurationException('Zendesk settings are not complete');
        }

        $client = new ZendeskAPI($zendeskSettings['subdomain']);
        $client->setAuth('basic', [
            'username' => $zendeskSettings['username'],
            'token' => $zendeskSettings['token'],
        ]);

        return $client;
    }
}

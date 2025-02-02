<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
use Parthenon\Common\Config;
use Parthenon\Common\LoggerAwareTrait;
use Zendesk\API\HttpClient as ZendeskAPI;

class ZendeskIntegration implements IntegrationInterface, CustomerSupportIntegrationInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private Config $config,
    ) {
    }

    public function setup(): void
    {
        $this->getLogger()->info('Setting up Zendesk integration');

        $client = $this->buildZendeskClient();
        $found = false;
        $contactFields = $client->userFields()->findAll();
        foreach ($contactFields->user_fields as $field) {
            if ('billabear_url' === $field->key) {
                $found = true;
                break;
            }
        }
        if ($found) {
            return;
        }

        $response = $client->userFields()->create([
            'key' => 'billabear_url',
            'title' => 'BillaBear URL',
            'type' => 'text',
            'description' => 'The URL to BillaBear',
            'active' => true,
        ]);
        $response = $client->userFields()->create([
            'key' => 'billing_reference',
            'title' => 'Billing Reference',
            'type' => 'text',
            'description' => 'The reference for the customer in the billing system',
            'active' => true,
        ]);
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        $client = $this->buildZendeskClient();
        $service = new CustomerService($client, $this->config);
        $service->setLogger($this->getLogger());

        return $service;
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
            [
                'name' => 'token',
                'label' => 'app.customer_support.integration.zendesk.token',
                'type' => 'text',
                'required' => true,
            ],
        ];
    }

    public function getAccountId(): string
    {
        throw new UnsupportedFeatureException('Zendesk does not support account id');
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

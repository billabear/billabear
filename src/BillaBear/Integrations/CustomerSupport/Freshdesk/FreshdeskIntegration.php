<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Freshdesk;

use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\CustomerSupport\CustomerServiceInterface;
use BillaBear\Integrations\CustomerSupport\CustomerSupportIntegrationInterface;
use BillaBear\Integrations\CustomerSupport\Freshdesk\Client\ContactService;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\Config;
use Parthenon\Common\LoggerAwareTrait;

class FreshdeskIntegration implements IntegrationInterface, CustomerSupportIntegrationInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private Config $config,
    ) {
    }

    public function setup(): void
    {
        $client = $this->buildClient();

        $customFields = $client->allCustomFields();
        $found = false;
        foreach ($customFields as $customField) {
            if ('billabear_url' === $customField['name']) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $client->createCustomField([
                'label' => 'Billabear URL',
                'label_for_customers' => 'Billabear URL',
                'type' => 'custom_text',
                'customers_can_edit' => false,
                'displayed_for_customers' => false,
            ]);
        }
    }

    public function getType(): IntegrationType
    {
        return IntegrationType::CUSTOMER_SUPPORT;
    }

    public function getName(): string
    {
        return 'freshdesk';
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::API_KEY;
    }

    public function getOauthConfig(): OauthConfig
    {
        throw new UnsupportedFeatureException('Freshdesk does not support OAuth');
    }

    public function getSettings(): array
    {
        return [
            [
                'name' => 'subdomain',
                'label' => 'app.customer_support.integration.freshdesk.subdomain',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'api_key',
                'label' => 'app.customer_support.integration.freshdesk.api_key',
                'type' => 'text',
                'required' => true,
            ],
        ];
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        $customerService = new CustomerService($this->buildClient(), $this->config);
        $customerService->setLogger($this->logger);

        return $customerService;
    }

    private function buildClient(): ContactService
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        $customerSupportSettings = $settings->getCustomerSupportIntegration()->getSettings();

        $client = new ContactService(
            $customerSupportSettings['api_key'],
            $customerSupportSettings['subdomain'],
        );
        $client->setLogger($this->getLogger());

        return $client;
    }
}

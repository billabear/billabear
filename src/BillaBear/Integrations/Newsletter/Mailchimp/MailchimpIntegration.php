<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\Mailchimp;

use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\Newsletter\CustomerServiceInterface;
use BillaBear\Integrations\Newsletter\ListServiceInterface;
use BillaBear\Integrations\Newsletter\NewsletterIntegrationInterface;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;
use MailchimpMarketing\ApiClient;
use Parthenon\Common\LoggerAwareTrait;

class MailchimpIntegration implements IntegrationInterface, NewsletterIntegrationInterface
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
        return 'mailchimp';
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::API_KEY;
    }

    public function getOauthConfig(): OauthConfig
    {
        throw new UnsupportedFeatureException('Mailchimp does not support OAuth');
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
            [
                'name' => 'server_prefix',
                'label' => 'app.integrations.newsletter.mailchimp.fields.server_prefix',
                'type' => 'text',
                'required' => true,
            ],
        ];
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        $customerService = new CustomerService($this->buildClient());
        $customerService->setLogger($this->getLogger());

        return $customerService;
    }

    public function getListService(): ListServiceInterface
    {
        $listService = new ListService($this->buildClient());
        $listService->setLogger($this->getLogger());

        return $listService;
    }

    public function getAccountId(): string
    {
        throw new UnsupportedFeatureException('Mailchimp does not support account id');
    }

    private function buildClient(): ApiClient
    {
        $newsletterSettings = $this->settingsRepository->getDefaultSettings()->getNewsletterIntegration()->getSettings();
        $mailchimp = new ApiClient();

        if (!isset($newsletterSettings['api_key']) || !isset($newsletterSettings['server_prefix'])) {
            throw new \InvalidArgumentException('Mailchimp API key and server prefix must be set');
        }

        $mailchimp->setConfig([
            'apiKey' => $newsletterSettings['api_key'],
            'server' => $newsletterSettings['server_prefix'],
        ]);

        return $mailchimp;
    }
}

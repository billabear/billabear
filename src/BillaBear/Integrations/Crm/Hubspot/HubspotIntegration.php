<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Hubspot;

use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\Crm\CrmIntegrationInterface;
use BillaBear\Integrations\Crm\CustomerServiceInterface;
use BillaBear\Integrations\Crm\EmailServiceInterface;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\Oauth\OauthConnectionProvider;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;
use HubSpot\Discovery\Discovery;
use HubSpot\Factory;
use Parthenon\Common\Config;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class HubspotIntegration implements CrmIntegrationInterface, IntegrationInterface
{
    use LoggerAwareTrait;

    private Discovery $client;

    public function __construct(
        #[Autowire(env: 'HUBSPOT_CLIENT_ID')]
        private string $clientId,
        #[Autowire(env: 'HUBSPOT_CLIENT_SECRET')]
        private string $cientSecret,
        #[Autowire(env: 'HUBSPOT_REDIRECT_URI')]
        private string $redirectUri,
        private SettingsRepositoryInterface $settingsRepository,
        private OauthConnectionProvider $oauthConnectionProvider,
        private Config $config,
    ) {
    }

    public function setup(): void
    {
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        $customerService = new CustomerService($this->getClient());
        $customerService->setLogger($this->getLogger());

        return $customerService;
    }

    public function getEmailService(): EmailServiceInterface
    {
        $emailService = new EmailService($this->getClient());
        $emailService->setLogger($this->getLogger());

        return $emailService;
    }

    public function getType(): IntegrationType
    {
        return IntegrationType::CRM;
    }

    public function getName(): string
    {
        return 'hubspot';
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::OAUTH;
    }

    public function getOauthConfig(): OauthConfig
    {
        return new OauthConfig(
            $this->clientId,
            $this->cientSecret,
            $this->redirectUri,
            'https://app.hubspot.com/oauth/authorize',
            'https://api.hubapi.com/oauth/v1/token',
            '',
            'oauth files crm.objects.custom.read crm.objects.custom.write crm.objects.companies.read crm.objects.companies.write crm.objects.contacts.read crm.objects.contacts.write crm.objects.invoices.read crm.objects.orders.read crm.objects.orders.write crm.objects.quotes.read crm.objects.quotes.write crm.schemas.quotes.read'
        );
    }

    public function getSettings(): array
    {
        return [];
    }

    private function getClient(): Discovery
    {
        if (!isset($this->client)) {
            $this->client = Factory::createWithAccessToken($this->oauthConnectionProvider->getAccessToken($this));
        }

        return $this->client;
    }
}

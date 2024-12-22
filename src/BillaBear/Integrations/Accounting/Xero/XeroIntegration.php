<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Integrations\Accounting\AccountingIntegrationInterface;
use BillaBear\Integrations\Accounting\CustomerInterface;
use BillaBear\Integrations\Accounting\InvoiceInterface;
use BillaBear\Integrations\Accounting\VoucherInterface;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\Oauth\OauthConnectionProvider;
use BillaBear\Integrations\OAuthConfig;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use XeroAPI\XeroPHP\Configuration;

class XeroIntegration implements IntegrationInterface, AccountingIntegrationInterface
{
    use LoggerAwareTrait;

    public const INTEGRATION_NAME = 'xero';

    private ClientInterface $client;

    private string $tenantId;

    public function __construct(
        #[Autowire(env: 'XERO_CLIENT_ID')]
        private string $clientId,
        #[Autowire(env: 'XERO_CLIENT_SECRET')]
        private string $cientSecret,
        #[Autowire(env: 'XERO_REDIRECT_URI')]
        private string $redirectUri,
        private OauthConnectionProvider $oauthConnectionProvider,
    ) {
    }

    public function getType(): IntegrationType
    {
        return IntegrationType::ACCOUNTING;
    }

    public function getName(): string
    {
        return self::INTEGRATION_NAME;
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::OAUTH;
    }

    public function getOauthConfig(): OAuthConfig
    {
        return new OAuthConfig(
            $this->clientId,
            $this->cientSecret,
            $this->redirectUri,
            'https://login.xero.com/identity/connect/authorize',
            'https://login.xero.com/identity/connect/token',
            'https://api.xero.com/api.xro/2.0/Organisation',
            'openid email profile offline_access accounting.transactions accounting.contacts accounting.attachments'
        );
    }

    public function getInvoiceService(): InvoiceInterface
    {
    }

    public function getVoucherService(): VoucherInterface
    {
        // TODO: Implement getVoucherService() method.
    }

    public function getCustomerService(): CustomerInterface
    {
        $config = $this->createConfig();

        $customerService = new CustomerService($config, $this->createClient());
        $customerService->setLogger($this->getLogger());

        return $customerService;
    }

    private function createConfig(): Configuration
    {
        return Configuration::getDefaultConfiguration()->setAccessToken($this->oauthConnectionProvider->getAccessToken());
    }

    private function createClient(): ClientInterface
    {
        if (!isset($this->client)) {
            $this->client = new Client();
        }

        return $this->client;
    }

    private function getTenantId(): string
    {
        if (!isset($this->tenantId)) {
            $identityApi = new \XeroAPI\XeroPHP\Api\IdentityApi(
                $this->createClient(),
                $this->createConfig()
            );

            $result = $identityApi->getConnections();
            $this->tenantId = $result[0]->getTenantId();
        }

        return $this->tenantId;
    }
}

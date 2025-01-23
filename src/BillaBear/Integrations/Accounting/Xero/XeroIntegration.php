<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Integrations\Accounting\AccountingIntegrationInterface;
use BillaBear\Integrations\Accounting\CreditServiceInterface;
use BillaBear\Integrations\Accounting\CustomerServiceInterface;
use BillaBear\Integrations\Accounting\InvoiceServiceInterface;
use BillaBear\Integrations\Accounting\PaymentServiceInterface;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\Oauth\OauthConnectionProvider;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;
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
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function setup(): void
    {
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

    public function getOauthConfig(): OauthConfig
    {
        return new OauthConfig(
            $this->clientId,
            $this->cientSecret,
            $this->redirectUri,
            'https://login.xero.com/identity/connect/authorize',
            'https://login.xero.com/identity/connect/token',
            'https://api.xero.com/api.xro/2.0/Organisation',
            'openid email profile offline_access accounting.transactions accounting.contacts accounting.attachments'
        );
    }

    public function getInvoiceService(): InvoiceServiceInterface
    {
        $config = $this->createConfig();
        $invoiceService = new InvoiceService($this->getTenantId(), $config, $this->createClient());
        $invoiceService->setLogger($this->getLogger());

        return $invoiceService;
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        $config = $this->createConfig();

        $customerService = new CustomerService($this->getTenantId(), $config, $this->createClient());
        $customerService->setLogger($this->getLogger());

        return $customerService;
    }

    public function getPaymentService(): PaymentServiceInterface
    {
        $config = $this->createConfig();
        $settings = $this->settingsRepository->getDefaultSettings();
        $accountCode = $settings->getAccountingIntegration()->getSettings()['account_id'] ?? null;

        if (!isset($accountCode)) {
            throw new \Exception('Account code is not set');
        }

        $paymentService = new PaymentService($this->getTenantId(), (string) $accountCode, $config, $this->createClient());
        $paymentService->setLogger($this->getLogger());

        return $paymentService;
    }

    public function getSettings(): array
    {
        return [
            [
                'name' => 'account_id',
                'label' => 'app.finance.integration.xero.account_id',
                'type' => 'text',
                'required' => true,
            ],
        ];
    }

    public function getCreditService(): CreditServiceInterface
    {
        $config = $this->createConfig();
        $settings = $this->settingsRepository->getDefaultSettings();
        $accountCode = $settings->getAccountingIntegration()->getSettings()['account_id'] ?? null;

        if (!isset($accountCode)) {
            throw new \Exception('Account code is not set');
        }

        $refundService = new CreditService($this->getTenantId(), (string) $accountCode, $config, $this->createClient());
        $refundService->setLogger($this->getLogger());

        return $refundService;
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

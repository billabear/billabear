<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\EasyBill;

use BillaBear\Exception\Integrations\MissingConfigurationException;
use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\Accounting\AccountingIntegrationInterface;
use BillaBear\Integrations\Accounting\CreditServiceInterface;
use BillaBear\Integrations\Accounting\CustomerServiceInterface;
use BillaBear\Integrations\Accounting\InvoiceServiceInterface;
use BillaBear\Integrations\Accounting\PaymentServiceInterface;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;
use easybill\SDK\Client;
use easybill\SDK\Endpoint;
use Parthenon\Common\LoggerAwareTrait;

class EasyBillIntegration implements IntegrationInterface, AccountingIntegrationInterface
{
    use LoggerAwareTrait;

    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
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
        return 'easybill';
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::API_KEY;
    }

    public function getOauthConfig(): OauthConfig
    {
        throw new UnsupportedFeatureException('EasyBill does not support OAuth');
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

    public function getInvoiceService(): InvoiceServiceInterface
    {
        $invoiceService = new InvoiceService($this->createClient());
        $invoiceService->setLogger($this->getLogger());

        return $invoiceService;
    }

    public function getCustomerService(): CustomerServiceInterface
    {
        $customerService = new CustomerService($this->createClient());
        $customerService->setLogger($this->getLogger());

        return $customerService;
    }

    public function getPaymentService(): PaymentServiceInterface
    {
        // TODO: Implement getPaymentService() method.
    }

    public function getCreditService(): CreditServiceInterface
    {
        // TODO: Implement getCreditService() method.
    }

    private function createClient(): Client
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        $accountingSettings = $settings->getAccountingIntegration()->getSettings();

        if (!isset($accountingSettings['api_key'])) {
            throw new MissingConfigurationException('EasyBill API key is missing');
        }

        return new Client(new Endpoint($accountingSettings['api_key']));
    }
}

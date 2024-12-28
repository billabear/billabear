<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Zendesk;

use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\CustomerSupport\CustomerServiceInterface;
use BillaBear\Integrations\CustomerSupport\CustomerSupportIntegrationInterface;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\OauthConfig;

class ZendeskIntegration implements IntegrationInterface, CustomerSupportIntegrationInterface
{
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
        ];
    }
}

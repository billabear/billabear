<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\OAuthConfig;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class XeroIntegration implements IntegrationInterface
{
    public const INTEGRATION_NAME = 'xero';

    public function __construct(
        #[Autowire(env: 'XERO_CLIENT_ID')]
        private string $clientId,
        #[Autowire(env: 'XERO_CLIENT_SECRET')]
        private string $cientSecret,
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
            'https://login.xero.com/identity/connect/authorize',
            'https://login.xero.com/identity/connect/token',
            'https://api.xero.com/api.xro/2.0/Organisation',
            'openid email profile offline_access accounting.transactions accounting.contacts accounting.attachments'
        );
    }
}

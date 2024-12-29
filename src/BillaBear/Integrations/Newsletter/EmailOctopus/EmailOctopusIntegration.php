<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\EmailOctopus;

use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Integrations\Newsletter\NewsletterIntegrationInterface;
use BillaBear\Integrations\OauthConfig;
use BillaBear\Repository\SettingsRepositoryInterface;

class EmailOctopusIntegration implements IntegrationInterface, NewsletterIntegrationInterface
{
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
        return 'emailoctopus';
    }

    public function getAuthenticationType(): AuthenticationType
    {
        return AuthenticationType::API_KEY;
    }

    public function getOauthConfig(): OauthConfig
    {
        throw new UnsupportedFeatureException('Oauth is not supported for this integration');
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

    public function getCustomerService()
    {
        // TODO: Implement getCustomerService() method.
    }

    public function getListService()
    {
        // TODO: Implement getListService() method.
    }
}
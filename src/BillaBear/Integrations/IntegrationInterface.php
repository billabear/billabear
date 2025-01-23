<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations;

use BillaBear\Exception\Integrations\MissingConfigurationException;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('billabear.integration')]
interface IntegrationInterface
{
    /**
     * @throws UnexpectedErrorException
     * @throws MissingConfigurationException
     */
    public function setup(): void;

    public function getType(): IntegrationType;

    public function getName(): string;

    public function getAuthenticationType(): AuthenticationType;

    /**
     * @throws UnsupportedFeatureException
     */
    public function getOauthConfig(): OauthConfig;

    public function getSettings(): array;
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Oauth;

use BillaBear\Integrations\IntegrationInterface;

trait ProviderTrait
{
    protected function getProvider(IntegrationInterface $integration): \League\OAuth2\Client\Provider\GenericProvider
    {
        $oauthConfig = $integration->getOauthConfig();

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $oauthConfig->clientId,
            'clientSecret' => $oauthConfig->clientSecret,
            'redirectUri' => $oauthConfig->redirectUri,
            'urlAuthorize' => $oauthConfig->urlAuthorize,
            'urlAccessToken' => $oauthConfig->urlAccessToken,
            'urlResourceOwnerDetails' => $oauthConfig->urlResourceOwnerDetails,
        ]);

        return $provider;
    }
}

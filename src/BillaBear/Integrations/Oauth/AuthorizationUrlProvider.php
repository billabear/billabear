<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Oauth;

use League\OAuth2\Client\Provider\GenericProvider;

class AuthorizationUrlProvider implements AuthorizationUrlProviderInterface
{
    public function getAuthUrl(GenericProvider $genericProvider, string $scope, array $state): string
    {
        $state = base64_encode(json_encode($state));

        return $genericProvider->getAuthorizationUrl([
            'scope' => $scope,
            'state' => $state,
        ]);
    }
}

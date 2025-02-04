<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Integration;

use BillaBear\Integrations\Oauth\OauthManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OauthController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/integration/{integrationName}/oauth/redirect', name: 'integration_oauth_redirect')]
    public function oauthRedirect(
        Request $request,
        OauthManagerInterface $oauthManager,
    ): Response {
        $this->getLogger()->info('Handling oauth redirect');

        return $oauthManager->handleRedirect($request);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}

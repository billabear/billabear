<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\Integrations\Oauth\OauthManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OauthController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/{integrationName}/oauth/start', name: 'integration_oauth_start')]
    public function oauthStart(
        Request $request,
        OauthManager $oauthManager,
    ): Response {
        $integrationName = $request->get('integrationName');

        $this->getLogger()->info('Starting oauth flow for integration', ['integration' => $integrationName]);

        $redirect = $oauthManager->sendToIntegration($integrationName);

        return $redirect;
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}

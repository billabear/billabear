<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\Integrations\Oauth\OauthManager;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OauthController
{
    use LoggerAwareTrait;

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
}

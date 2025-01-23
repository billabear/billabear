<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller;

use Parthenon\Billing\Webhook\RequestProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController
{
    #[Route('/webhook', name: 'app_webhook')]
    public function webhook(Request $request, RequestProcessor $requestProcessor, LoggerInterface $logger): Response
    {
        $logger->info('Webhook call received');
        $requestProcessor->processRequest($request);

        return new JsonResponse([]);
    }
}

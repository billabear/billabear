<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller;

use Parthenon\Billing\Webhook\RequestProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController
{
    #[Route('/webhook', name: 'app_webhook')]
    public function webhook(Request $request, RequestProcessor $requestProcessor, LoggerInterface $logger)
    {
        $logger->info('Webhook call received');
        $requestProcessor->processRequest($request);

        return new JsonResponse([]);
    }
}

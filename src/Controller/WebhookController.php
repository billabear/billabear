<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

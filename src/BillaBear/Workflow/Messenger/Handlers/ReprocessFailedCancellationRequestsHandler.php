<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Subscription\CancellationRequestProcessor;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedCancellationRequests;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReprocessFailedCancellationRequestsHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
        private CancellationRequestProcessor $cancellationRequestProcessor,
    ) {
    }

    public function __invoke(ReprocessFailedCancellationRequests $generateNewInvoices)
    {
        $this->getLogger()->info('Started to process failed cancellation requests');
        $failed = $this->cancellationRequestRepository->getFailedProcesses();

        foreach ($failed as $request) {
            $this->getLogger()->info('Processing a failed cancellation request', ['cancellation_request' => (string) $request->getId()]);
            $this->cancellationRequestProcessor->process($request);
        }
    }
}

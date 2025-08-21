<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Subscription\CancellationRequestProcessor;
use BillaBear\Workflow\Messenger\Messages\ProcessCancellationRequest;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessCancellationRequestHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
        private CancellationRequestProcessor $cancellationRequestProcessor,
    ) {
    }

    public function __invoke(ProcessCancellationRequest $request): void
    {
        $this->getLogger()->info('Handling processing cancellation request', ['cancellation_process_id' => $request->id]);
        $process = $this->cancellationRequestRepository->findById($request->id);
        $this->cancellationRequestProcessor->process($process);
    }
}

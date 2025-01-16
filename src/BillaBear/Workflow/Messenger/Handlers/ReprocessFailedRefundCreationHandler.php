<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Payment\RefundCreatedProcessor;
use BillaBear\Repository\RefundCreatedProcessRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedRefundCreation;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReprocessFailedRefundCreationHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private RefundCreatedProcessRepositoryInterface $repository,
        private RefundCreatedProcessor $refundCreatedProcessor,
    ) {
    }

    public function __invoke(ReprocessFailedRefundCreation $creation)
    {
        $this->getLogger()->info('Started to process failed refund creations');
        $failed = $this->repository->getFailedProcesses();

        foreach ($failed as $request) {
            $this->getLogger()->info('Processing a failed refund creation', ['refund_creation' => (string) $request->getId()]);
            $this->refundCreatedProcessor->process($request);
        }
    }
}

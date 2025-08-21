<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Payment\RefundCreatedProcessor;
use BillaBear\Repository\RefundCreatedProcessRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ProcessRefundCreation;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessRefundCreationHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private RefundCreatedProcessRepositoryInterface $repository,
        private RefundCreatedProcessor $refundCreatedProcessor,
    ) {
    }

    public function __invoke(ProcessRefundCreation $created)
    {
        $this->getLogger()->info('Started to process refund creation', ['refund_creation_id' => $created->id]);
        $process = $this->repository->findById($created->id);
        $this->refundCreatedProcessor->process($process);
    }
}

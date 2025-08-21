<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Payment\PaymentCreationProcessor;
use BillaBear\Repository\PaymentCreationRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ProcessPaymentCreated;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessPaymentCreationHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private PaymentCreationRepositoryInterface $paymentCreationRepository,
        private PaymentCreationProcessor $paymentCreationProcessor,
    ) {
    }

    public function __invoke(ProcessPaymentCreated $created): void
    {
        $this->getLogger()->info('Started to process payment creation', ['payment_creation' => $created->id]);
        $process = $this->paymentCreationRepository->findById($created->id);
        $this->paymentCreationProcessor->process($process);
    }
}

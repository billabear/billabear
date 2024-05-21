<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Payment\PaymentCreationProcessor;
use BillaBear\Repository\PaymentCreationRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedPaymentCreation;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReprocessFailedPaymentCreationHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private PaymentCreationRepositoryInterface $paymentCreationRepository,
        private PaymentCreationProcessor $paymentCreationProcessor,
    ) {
    }

    public function __invoke(ReprocessFailedPaymentCreation $command)
    {
        $this->getLogger()->info('Started to process failed payment creations');
        $failed = $this->paymentCreationRepository->getFailedProcesses();

        foreach ($failed as $request) {
            $this->getLogger()->info('Processing a failed payment creation', ['payment_creation' => (string) $request->getId()]);
            $this->paymentCreationProcessor->process($request);
        }
    }
}

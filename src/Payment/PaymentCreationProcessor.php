<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Payment;

use App\Entity\PaymentCreation;
use App\Repository\PaymentCreationRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class PaymentCreationProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['create_receipt', 'generate_report_data', 'send_customer_notice', 'send_internal_notice'];

    public function __construct(
        private WorkflowInterface $paymentCreationStateMachine,
        private PaymentCreationRepositoryInterface $paymentCreationRepository,
    ) {
    }

    public function process(PaymentCreation $paymentCreation): void
    {
        $paymentCreationStateMachine = $this->paymentCreationStateMachine;

        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($paymentCreationStateMachine->can($paymentCreation, $transition)) {
                    $paymentCreationStateMachine->apply($paymentCreation, $transition);

                    $this->getLogger()->info('Did payment creation transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do payment creation transition", ['transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Payment creation transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $paymentCreation->setError($e->getMessage());
        }

        $this->paymentCreationRepository->save($paymentCreation);
    }
}
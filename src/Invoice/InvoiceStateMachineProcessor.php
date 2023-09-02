<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

use App\Entity\Processes\InvoiceProcess;
use App\Repository\Processes\InvoiceProcessRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class InvoiceStateMachineProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['send_customer_notifications', 'send_internal_notifications'];

    public function __construct(
        private WorkflowInterface $invoiceProcessStateMachine,
        private InvoiceProcessRepositoryInterface $invoiceProcessRepository,
    ) {
    }

    public function process(InvoiceProcess $request): void
    {
        $invoiceProcessStateMachine = $this->invoiceProcessStateMachine;

        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($invoiceProcessStateMachine->can($request, $transition)) {
                    $invoiceProcessStateMachine->apply($request, $transition);

                    $this->getLogger()->info('Did invoice process transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do invoice process transition", ['transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Invoice process transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $request->setError($e->getMessage());
        }

        $this->invoiceProcessRepository->save($request);
    }
}

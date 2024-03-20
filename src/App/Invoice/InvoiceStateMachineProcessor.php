<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

    public function process(InvoiceProcess $invoiceProcess): void
    {
        $invoiceProcessStateMachine = $this->invoiceProcessStateMachine;

        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($invoiceProcessStateMachine->can($invoiceProcess, $transition)) {
                    $invoiceProcessStateMachine->apply($invoiceProcess, $transition);

                    $this->getLogger()->info('Did invoice process transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do invoice process transition", ['transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Invoice process transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $invoiceProcess->setError($e->getMessage());
        }
        $invoiceProcess->setUpdatedAt(new \DateTime('now'));

        $this->invoiceProcessRepository->save($invoiceProcess);
    }

    public function processPaid(InvoiceProcess $invoiceProcess): void
    {
        $invoiceProcessStateMachine = $this->invoiceProcessStateMachine;
        try {
            if ($invoiceProcessStateMachine->can($invoiceProcess, 'mark_as_paid')) {
                $invoiceProcessStateMachine->apply($invoiceProcess, 'mark_as_paid');

                $this->getLogger()->info('Did invoice process transition', ['transition' => 'mark_as_paid']);
            } else {
                $this->getLogger()->info("Can't do invoice process transition", ['transition' => 'mark_as_paid']);
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Invoice process transition failed', ['transition' => 'mark_as_paid', 'message' => $e->getMessage()]);
            $invoiceProcess->setError($e->getMessage());
        }
        $invoiceProcess->setUpdatedAt(new \DateTime('now'));

        $this->invoiceProcessRepository->save($invoiceProcess);
    }

    public function processDisableCustomer(InvoiceProcess $invoiceProcess): void
    {
        $invoiceProcessStateMachine = $this->invoiceProcessStateMachine;
        try {
            if ($invoiceProcessStateMachine->can($invoiceProcess, 'disable_customer')) {
                $invoiceProcessStateMachine->apply($invoiceProcess, 'disable_customer');

                $this->getLogger()->info('Did invoice process transition', ['transition' => 'disable_customer']);
            } else {
                $this->getLogger()->info("Can't do invoice process transition", ['transition' => 'disable_customer']);
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Invoice process transition failed', ['transition' => 'disable_customer', 'message' => $e->getMessage()]);
            $invoiceProcess->setError($e->getMessage());
        }
        $invoiceProcess->setUpdatedAt(new \DateTime('now'));

        $this->invoiceProcessRepository->save($invoiceProcess);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\EventSubscriber;

use App\Event\InvoiceCreated;
use App\Invoice\InvoiceStateMachineProcessor;
use App\Repository\Processes\InvoiceProcessRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoicePaidSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private InvoiceProcessRepositoryInterface $invoiceProcessRepository,
        private InvoiceStateMachineProcessor $invoiceStateMachineProcessor,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            InvoiceCreated::NAME => [
                'handlePaidInvoice',
            ],
        ];
    }

    public function handlePaidInvoice(InvoiceCreated $created)
    {
        $invoice = $created->getInvoice();
        $invoiceProcess = $this->invoiceProcessRepository->getForInvoice($invoice);

        $this->invoiceStateMachineProcessor->processPaid($invoiceProcess);
    }
}

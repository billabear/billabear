<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber;

use BillaBear\Event\Invoice\InvoicePaid;
use BillaBear\Invoice\InvoiceStateMachineProcessor;
use BillaBear\Repository\Processes\InvoiceProcessRepositoryInterface;
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
            InvoicePaid::NAME => [
                'handlePaidInvoice',
            ],
        ];
    }

    public function handlePaidInvoice(InvoicePaid $created): void
    {
        $invoice = $created->invoice;
        $invoiceProcess = $this->invoiceProcessRepository->getForInvoice($invoice);

        $this->invoiceStateMachineProcessor->processPaid($invoiceProcess);
    }
}

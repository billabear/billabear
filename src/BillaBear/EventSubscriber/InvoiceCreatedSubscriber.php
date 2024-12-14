<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber;

use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Event\Invoice\InvoiceCreated;
use BillaBear\Invoice\InvoiceStateMachineProcessor;
use BillaBear\Repository\Processes\InvoiceProcessRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoiceCreatedSubscriber implements EventSubscriberInterface
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
                'handleNewInvoice',
            ],
        ];
    }

    public function handleNewInvoice(InvoiceCreated $created)
    {
        $invoice = $created->invoice;

        $invoiceProcess = new InvoiceProcess();
        $invoiceProcess->setState('started');
        $invoiceProcess->setCustomer($invoice->getCustomer());
        $invoiceProcess->setInvoice($invoice);
        $invoiceProcess->setCreatedAt(new \DateTime('now'));
        $invoiceProcess->setUpdatedAt(new \DateTime('now'));
        $invoiceProcess->setDueAt($invoice->getDueAt());

        $this->invoiceProcessRepository->save($invoiceProcess);

        $this->invoiceStateMachineProcessor->process($invoiceProcess);
    }
}

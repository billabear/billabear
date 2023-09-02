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

namespace App\EventSubscriber;

use App\Entity\Processes\InvoiceProcess;
use App\Event\InvoiceCreated;
use App\Invoice\InvoiceStateMachineProcessor;
use App\Repository\Processes\InvoiceProcessRepositoryInterface;
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
        $invoice = $created->getInvoice();

        $invoiceProcess = new InvoiceProcess();
        $invoiceProcess->setState('started');
        $invoiceProcess->setCustomer($invoice->getCustomer());
        $invoiceProcess->setInvoice($invoice);
        $invoiceProcess->setCreatedAt(new \DateTime('now'));
        $invoiceProcess->setUpdatedAt(new \DateTime('now'));

        $this->invoiceProcessRepository->save($invoiceProcess);

        $this->invoiceStateMachineProcessor->process($invoiceProcess);
    }
}

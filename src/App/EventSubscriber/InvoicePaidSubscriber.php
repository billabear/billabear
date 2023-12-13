<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

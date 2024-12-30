<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Invoice;

use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Integrations\Accounting\Action\SyncInvoice;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SyncIntegrationTransition implements EventSubscriberInterface
{
    public function __construct(private SyncInvoice $syncInvoice)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.invoice_process.transition.sync_with_integration' => ['transition'],
        ];
    }

    public function transition(Event $event)
    {
        /** @var InvoiceProcess $invoice */
        $invoice = $event->getSubject();
        $this->syncInvoice->sync($invoice->getInvoice());
    }
}

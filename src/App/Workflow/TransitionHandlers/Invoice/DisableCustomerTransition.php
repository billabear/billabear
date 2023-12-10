<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\TransitionHandlers\Invoice;

use App\Customer\Disabler;
use App\Entity\Processes\InvoiceProcess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class DisableCustomerTransition implements EventSubscriberInterface
{
    public function __construct(
        private Disabler $disabler,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.invoice_process.transition.disable_customer' => ['transition'],
        ];
    }

    public function transition(Event $event)
    {
        /** @var InvoiceProcess $invoiceProcess */
        $invoiceProcess = $event->getSubject();
        $this->disabler->disable($invoiceProcess->getCustomer());
    }
}

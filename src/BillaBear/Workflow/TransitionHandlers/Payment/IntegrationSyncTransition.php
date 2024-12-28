<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Payment;

use BillaBear\Integrations\Accounting\Action\SyncPayment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class IntegrationSyncTransition implements EventSubscriberInterface
{
    public function __construct(private SyncPayment $syncPayment)
    {
    }

    public function transition(Event $event): void
    {
        $paymentCreation = $event->getSubject();
        $this->syncPayment->sync($paymentCreation->getPayment());
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_payment.transition.sync_with_integration' => ['transition'],
        ];
    }
}
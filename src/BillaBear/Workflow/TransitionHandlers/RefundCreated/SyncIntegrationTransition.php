<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\RefundCreated;

use BillaBear\Entity\RefundCreatedProcess;
use BillaBear\Integrations\Accounting\Action\SyncRefund;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SyncIntegrationTransition implements EventSubscriberInterface
{
    public function __construct(private SyncRefund $syncRefund)
    {
    }

    public function transition(Event $event)
    {
        /** @var RefundCreatedProcess $refundCreatedProcess */
        $refundCreatedProcess = $event->getSubject();
        $refund = $refundCreatedProcess->getRefund();
        $this->syncRefund->sync($refund);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_refund.transition.sync_with_integration' => ['transition'],
        ];
    }
}

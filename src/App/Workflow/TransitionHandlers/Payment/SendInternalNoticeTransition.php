<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\Payment;

use App\Entity\PaymentCreation;
use App\Webhook\Outbound\EventDispatcherInterface;
use App\Webhook\Outbound\Payload\PaymentReceivedPayload;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SendInternalNoticeTransition implements EventSubscriberInterface
{
    public function __construct(private EventDispatcherInterface $eventDisptacher)
    {
    }

    public function transition(Event $event)
    {
        /** @var PaymentCreation $paymentCreation */
        $paymentCreation = $event->getSubject();
        $payment = $paymentCreation->getPayment();
        $payload = new PaymentReceivedPayload($payment);
        $this->eventDisptacher->dispatch($payload);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_payment.transition.send_internal_notice' => ['transition'],
        ];
    }
}

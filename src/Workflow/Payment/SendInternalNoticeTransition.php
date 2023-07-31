<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\Payment;

use App\Entity\PaymentCreation;
use App\Webhook\Outbound\EventProcessor;
use App\Webhook\Outbound\Payload\PaymentReceivedPayload;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SendInternalNoticeTransition implements EventSubscriberInterface
{
    public function __construct(private EventProcessor $eventProcessor)
    {
    }

    public function transition(Event $event)
    {
        /** @var PaymentCreation $paymentCreation */
        $paymentCreation = $event->getSubject();
        $payment = $paymentCreation->getPayment();
        $payload = new PaymentReceivedPayload($payment);
        $this->eventProcessor->process($payload);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.payment_creation.transition.send_internal_notice' => ['transition'],
        ];
    }
}

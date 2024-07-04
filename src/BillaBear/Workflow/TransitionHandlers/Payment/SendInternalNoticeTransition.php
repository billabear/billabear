<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Payment;

use BillaBear\Entity\PaymentCreation;
use BillaBear\Notification\Slack\Data\PaymentProcessed;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Webhook\Outbound\Payload\PaymentReceivedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SendInternalNoticeTransition implements EventSubscriberInterface
{
    public function __construct(
        private NotificationSender $notificationSender,
        private WebhookDispatcherInterface $webhookDisptacher)
    {
    }

    public function transition(Event $event)
    {
        /** @var PaymentCreation $paymentCreation */
        $paymentCreation = $event->getSubject();
        $payment = $paymentCreation->getPayment();
        $payload = new PaymentReceivedPayload($payment);
        $this->webhookDisptacher->dispatch($payload);

        $notificationMessage = new PaymentProcessed($payment);

        $this->notificationSender->sendNotification($notificationMessage);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_payment.transition.send_internal_notice' => ['transition'],
        ];
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Payment;

use BillaBear\Entity\PaymentCreation;
use BillaBear\Enum\SlackNotificationEvent;
use BillaBear\Notification\Slack\Data\PaymentProcessed;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use BillaBear\Webhook\Outbound\EventDispatcherInterface;
use BillaBear\Webhook\Outbound\Payload\PaymentReceivedPayload;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SendInternalNoticeTransition implements EventSubscriberInterface
{
    public function __construct(
        private SlackNotificationRepositoryInterface $slackNotificationRepository,
        private NotificationSender $notificationSender,
        private EventDispatcherInterface $eventDisptacher)
    {
    }

    public function transition(Event $event)
    {
        /** @var PaymentCreation $paymentCreation */
        $paymentCreation = $event->getSubject();
        $payment = $paymentCreation->getPayment();
        $payload = new PaymentReceivedPayload($payment);
        $this->eventDisptacher->dispatch($payload);

        $notifications = $this->slackNotificationRepository->findActiveForEvent(SlackNotificationEvent::PAYMENT_PROCESSED);
        $notificationMessage = new PaymentProcessed($payment);

        foreach ($notifications as $notification) {
            $this->notificationSender->sendNotification($notification->getSlackWebhook(), $notificationMessage);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.payment_failure_process.transition.send_internal_notice' => ['transition'],
        ];
    }
}

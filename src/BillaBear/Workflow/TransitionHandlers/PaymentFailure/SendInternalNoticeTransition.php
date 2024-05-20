<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\PaymentFailure;

use BillaBear\Entity\PaymentFailureProcess;
use BillaBear\Enum\SlackNotificationEvent;
use BillaBear\Notification\Slack\Data\PaymentFailure;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use BillaBear\Webhook\Outbound\EventDispatcherInterface;
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
        /** @var PaymentFailureProcess $paymentFailureProcess */
        $paymentFailureProcess = $event->getSubject();
        $paymentAttempt = $paymentFailureProcess->getPaymentAttempt();

        $notifications = $this->slackNotificationRepository->findActiveForEvent(SlackNotificationEvent::SUBSCRIPTION_CANCELLED);
        $notificationMessage = new PaymentFailure($paymentAttempt);

        foreach ($notifications as $notification) {
            $this->notificationSender->sendNotification($notification->getSlackWebhook(), $notificationMessage);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_payment.transition.send_internal_notice' => ['transition'],
        ];
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\PaymentFailure;

use BillaBear\Entity\PaymentFailureProcess;
use BillaBear\Notification\Slack\Data\PaymentFailure;
use BillaBear\Notification\Slack\NotificationSender;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SendInternalNoticeTransition implements EventSubscriberInterface
{
    public function __construct(
        private NotificationSender $notificationSender,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var PaymentFailureProcess $paymentFailureProcess */
        $paymentFailureProcess = $event->getSubject();
        $paymentAttempt = $paymentFailureProcess->getPaymentAttempt();
        $notificationMessage = new PaymentFailure($paymentAttempt);

        $this->notificationSender->sendNotification($notificationMessage);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.payment_failure_process.transition.send_internal_notice' => ['transition'],
        ];
    }
}

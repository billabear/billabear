<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrailEnded;

use BillaBear\Notification\Slack\Data\TrialEnded;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\Event\Event;

class SendInternalNotification
{
    use LoggerAwareTrait;

    public function __construct(
        private SlackNotificationRepositoryInterface $slackNotificationRepository,
        private NotificationSender $notificationSender,
    ) {
    }

    public function transition(Event $event)
    {
        $process = $event->getSubject();

        $subscription = $process->getSubscription();
        $notificationMessage = new TrialEnded($subscription);

        $this->notificationSender->sendNotification($notificationMessage);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.trial_ended.transition.send_internal_notice' => ['transition'],
        ];
    }
}

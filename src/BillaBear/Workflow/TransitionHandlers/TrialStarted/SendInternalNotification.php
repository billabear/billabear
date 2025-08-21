<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrialStarted;

use BillaBear\Notification\Slack\Data\TrialStarted;
use BillaBear\Notification\Slack\NotificationSender;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendInternalNotification implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly NotificationSender $notificationSender,
    ) {
    }

    public function transition(Event $event)
    {
        $process = $event->getSubject();

        $subscription = $process->getSubscription();
        $notificationMessage = new TrialStarted($subscription);

        $this->notificationSender->sendNotification($notificationMessage);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.trial_started.transition.send_internal_notice' => ['transition'],
        ];
    }
}

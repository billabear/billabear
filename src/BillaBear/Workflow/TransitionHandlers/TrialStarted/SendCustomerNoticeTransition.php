<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrialStarted;

use BillaBear\Entity\Processes\TrialStartedProcess;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Subscription\TrialStartedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcher;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendCustomerNoticeTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private WebhookDispatcher $eventDispatcher,
        private SlackNotificationRepositoryInterface $slackNotificationRepository,
        private NotificationSender $notificationSender,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var TrialStartedProcess $trialEnded */
        $trialEnded = $event->getSubject();
        $subscription = $trialEnded->getSubscription();
        $payload = new TrialStartedPayload($subscription);
        $this->eventDispatcher->dispatch($payload);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.trial_started.transition.send_customer_notice' => ['transition'],
        ];
    }
}

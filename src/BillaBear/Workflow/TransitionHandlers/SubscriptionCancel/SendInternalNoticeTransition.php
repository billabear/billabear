<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\SubscriptionCancel;

use BillaBear\Entity\CancellationRequest;
use BillaBear\Notification\Slack\Data\SubscriptionCancelled;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Subscription\SubscriptionCancelledPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SendInternalNoticeTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SlackNotificationRepositoryInterface $slackNotificationRepository,
        private NotificationSender $notificationSender,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function transition(Event $event)
    {
        $cancellationRequest = $event->getSubject();

        if (!$cancellationRequest instanceof CancellationRequest) {
            $this->getLogger()->error('Cancellation Request transition has something other than a CancellationRequest object');

            return;
        }

        $subscription = $cancellationRequest->getSubscription();
        $notificationMessage = new SubscriptionCancelled($subscription);

        $this->notificationSender->sendNotification($notificationMessage);
        $this->webhookDispatcher->dispatch(new SubscriptionCancelledPayload($subscription));
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.cancel_subscription.transition.send_internal_notice' => ['transition'],
        ];
    }
}

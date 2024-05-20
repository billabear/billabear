<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\SubscriptionCreation;

use BillaBear\Entity\SubscriptionCreation;
use BillaBear\Enum\SlackNotificationEvent;
use BillaBear\Notification\Slack\Data\SubscriptionCreated;
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
        $subscriptionCreation = $event->getSubject();

        if (!$subscriptionCreation instanceof SubscriptionCreation) {
            $this->getLogger()->error('Subscription creation transition has something other than a SubscriptionCreation object');

            return;
        }

        $subscription = $subscriptionCreation->getSubscription();
        $notifications = $this->slackNotificationRepository->findActiveForEvent(SlackNotificationEvent::SUBSCRIPTION_CREATED);
        $notificationMessage = new SubscriptionCreated($subscription);

        foreach ($notifications as $notification) {
            $this->notificationSender->sendNotification($notification->getSlackWebhook(), $notificationMessage);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_subscription.transition.send_internal_notice' => ['transition'],
        ];
    }
}

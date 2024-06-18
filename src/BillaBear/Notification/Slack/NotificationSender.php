<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack;

use BillaBear\Notification\Slack\Data\SlackNotificationInterface;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Slack\WebhookPosterInterface;

class NotificationSender
{
    use LoggerAwareTrait;

    public function __construct(
        private WebhookPosterInterface $webhookPoster,
        private SlackNotificationRepositoryInterface $slackNotificationRepository,
    ) {
    }

    public function sendNotification(SlackNotificationInterface $slackNotification)
    {
        $notifications = $this->slackNotificationRepository->findActiveForEvent($slackNotification->getEvent());

        foreach ($notifications as $notification) {
            $this->getLogger()->info('Sending slack notification', ['notification_id' => (string) $notification->getId(), 'event_type' => (string) $notification->getEvent()->value]);
            $this->webhookPoster->send($notification->getSlackWebhook()->getWebhookUrl(), $slackNotification->getMessage($notification));
        }
    }
}

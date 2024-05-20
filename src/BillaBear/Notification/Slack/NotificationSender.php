<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack;

use BillaBear\Entity\SlackWebhook;
use BillaBear\Notification\Slack\Data\SlackNotificationInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Slack\WebhookPosterInterface;

class NotificationSender
{
    use LoggerAwareTrait;

    public function __construct(private WebhookPosterInterface $webhookPoster)
    {
    }

    public function sendNotification(SlackWebhook $slackWebhook, SlackNotificationInterface $slackNotification)
    {
        $this->getLogger()->info('Sending slack notification');
        $this->webhookPoster->send($slackWebhook->getWebhookUrl(), $slackNotification->getMessage());
    }
}

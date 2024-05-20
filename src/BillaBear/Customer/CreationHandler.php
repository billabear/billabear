<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

use BillaBear\Entity\Customer;
use BillaBear\Enum\SlackNotificationEvent;
use BillaBear\Notification\Slack\Data\CustomerCreated;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use BillaBear\Webhook\Outbound\EventDispatcherInterface;
use BillaBear\Webhook\Outbound\Payload\CustomerCreatedPayload;

class CreationHandler
{
    public function __construct(
        private SlackNotificationRepositoryInterface $slackNotificationRepository,
        private NotificationSender $notificationSender,
        private EventDispatcherInterface $eventProcessor,
    ) {
    }

    public function handleCreation(Customer $customer): void
    {
        $this->eventProcessor->dispatch(new CustomerCreatedPayload($customer));
        $notifications = $this->slackNotificationRepository->findActiveForEvent(SlackNotificationEvent::CUSTOMER_CREATED);
        $notificationMessage = new CustomerCreated($customer);
        foreach ($notifications as $notification) {
            $this->notificationSender->sendNotification($notification->getSlackWebhook(), $notificationMessage);
        }
    }
}

<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\Country;
use BillaBear\Entity\State;
use BillaBear\Notification\Slack\Data\Tax\CountryThresholdReached as CountrySlack;
use BillaBear\Notification\Slack\Data\Tax\StateThresholdReached as StateSlack;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Webhook\Outbound\Payload\Tax\CountryThresholdReached as CountryWebhook;
use BillaBear\Webhook\Outbound\Payload\Tax\StateThresholdReached as StateWebhook;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

class ThresholdNotifier
{
    public function __construct(
        private WebhookDispatcherInterface $webhookDispatcher,
        private NotificationSender $slackNotifier,
    ) {
    }

    public function countryThresholdReached(Country $country): void
    {
        $this->slackNotifier->sendNotification(new CountrySlack($country));
        $this->webhookDispatcher->dispatch(new CountryWebhook($country));
    }

    public function stateThresholdReached(State $state): void
    {
        $this->slackNotifier->sendNotification(new StateSlack($state));
        $this->webhookDispatcher->dispatch(new StateWebhook($state));
    }
}

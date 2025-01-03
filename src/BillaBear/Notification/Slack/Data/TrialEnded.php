<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\Subscription;
use BillaBear\Notification\Slack\SlackNotificationEvent;

class TrialEnded extends AbstractNotification
{
    use CustomerTrait;
    use SubscriptionTrait;

    public function __construct(private Subscription $subscription)
    {
    }

    public function getEvent(): SlackNotificationEvent
    {
        return SlackNotificationEvent::TRIAL_ENDED;
    }

    protected function getData(): array
    {
        return [
            'customer' => $this->buildCustomerData($this->subscription->getCustomer()),
            'subscription' => $this->buildSubscriptionData($this->subscription),
        ];
    }
}

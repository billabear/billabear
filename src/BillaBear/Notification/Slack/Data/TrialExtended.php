<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\Subscription;
use BillaBear\Notification\Slack\SlackNotificationEvent;

class TrialExtended extends AbstractNotification
{
    use CustomerTrait;
    use SubscriptionTrait;

    public function __construct(private Subscription $subscription)
    {
    }

    public function getEvent(): SlackNotificationEvent
    {
        return SlackNotificationEvent::TRIAL_CONVERTED;
    }

    protected function getData(): array
    {
        return [
            'customer' => $this->buildCustomerData($this->subscription->getCustomer()),
            'subscription' => $this->buildSubscriptionData($this->subscription),
        ];
    }
}

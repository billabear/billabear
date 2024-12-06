<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit;
use BillaBear\Enum\SlackNotificationEvent;
use Brick\Money\Money;

class UsageDisable extends AbstractNotification
{
    use CustomerTrait;

    public function __construct(
        private Customer $customer,
        private UsageLimit $usageLimit,
        private Money $currentAmount,
    ) {
    }

    public function getEvent(): SlackNotificationEvent
    {
        return SlackNotificationEvent::USAGE_DISABLE;
    }

    protected function getData(): array
    {
        $limitAmount = Money::ofMinor($this->usageLimit->getAmount(), $this->currentAmount->getCurrency());

        return [
            'customer' => $this->buildCustomerData($this->customer),
            'limit_amount' => (string) $limitAmount,
            'current_amount' => (string) $this->currentAmount,
        ];
    }
}

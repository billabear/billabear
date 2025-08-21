<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\Customer;
use BillaBear\Notification\Slack\SlackNotificationEvent;

class CustomerCreated extends AbstractNotification
{
    use CustomerTrait;

    public function __construct(private Customer $customer)
    {
    }

    public function getEvent(): SlackNotificationEvent
    {
        return SlackNotificationEvent::CUSTOMER_CREATED;
    }

    protected function getData(): array
    {
        return [
            'customer' => $this->buildCustomerData($this->customer),
        ];
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\Payment;
use BillaBear\Notification\Slack\SlackNotificationEvent;

class PaymentProcessed extends AbstractNotification
{
    use CustomerTrait;

    public function __construct(private Payment $payment)
    {
    }

    public function getEvent(): SlackNotificationEvent
    {
        return SlackNotificationEvent::PAYMENT_PROCESSED;
    }

    protected function getData(): array
    {
        return [
            'customer' => $this->buildCustomerData($this->payment->getCustomer()),
            'payment' => [
                'amount' => $this->payment->getAmount(),
                'currency' => $this->payment->getCurrency(),
                'amount_formatted' => (string) $this->payment->getMoneyAmount(),
            ],
        ];
    }
}

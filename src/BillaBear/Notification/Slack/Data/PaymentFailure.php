<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\PaymentAttempt;
use BillaBear\Enum\SlackNotificationEvent;
use Brick\Money\Money;

class PaymentFailure extends AbstractNotification
{
    use CustomerTrait;

    public function __construct(private PaymentAttempt $paymentAttempt)
    {
    }

    public function getEvent(): SlackNotificationEvent
    {
        return SlackNotificationEvent::PAYMENT_FAILED;
    }

    protected function getData(): array
    {
        $money = Money::ofMinor($this->paymentAttempt->getAmount(), $this->paymentAttempt->getCurrency());

        return [
            'payment_attempt' => [
                'amount' => $this->paymentAttempt->getAmount(),
                'currency' => $this->paymentAttempt->getCurrency(),
                'formatted_amount' => (string) $money,
            ],
            'customer' => $this->buildCustomerData($this->paymentAttempt->getCustomer()),
        ];
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\Payment;
use Parthenon\Notification\Slack\MessageBuilder;

class PaymentProcessed implements SlackNotificationInterface
{
    public function __construct(private Payment $payment)
    {
    }

    public function getMessage(): array
    {
        $messageBuilder = new MessageBuilder();
        $messageBuilder->addTextSection('Payment successfully processed - '.(string) $this->payment->getMoneyAmount())->closeSection();

        return $messageBuilder->build();
    }
}

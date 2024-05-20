<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\PaymentAttempt;
use Parthenon\Notification\Slack\MessageBuilder;

class PaymentFailure implements SlackNotificationInterface
{
    public function __construct(private PaymentAttempt $paymentAttempt)
    {
    }

    public function getMessage(): array
    {
        $messageBuilder = new MessageBuilder();
        $messageBuilder->addTextSection('Payment processing failed - '.(string) $this->paymentAttempt->getAmount())->closeSection();

        return $messageBuilder->build();
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Usage\Warning;

use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit;
use BillaBear\Notification\Email\Data\UsageWarningEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Notification\Slack\Data\UsageWarning;
use BillaBear\Notification\Slack\NotificationSender;
use Brick\Money\Money;
use Parthenon\Notification\EmailSenderInterface;

class WarningNotifier
{
    public function __construct(
        private readonly EmailBuilder $emailBuilder,
        private readonly EmailSenderInterface $emailSender,
        private readonly NotificationSender $notificationSender,
    ) {
    }

    public function notify(Customer $customer, UsageLimit $usageLimit, Money $amount): void
    {
        $emailNotification = new UsageWarningEmail($usageLimit, $amount);
        $email = $this->emailBuilder->build($customer, $emailNotification);
        $this->emailSender->send($email);

        $slackNotification = new UsageWarning($customer, $usageLimit, $amount);
        $this->notificationSender->sendNotification($slackNotification);
    }
}

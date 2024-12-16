<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage\Warning;

use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit;
use BillaBear\Notification\Email\Data\UsageDisableEmail;
use BillaBear\Notification\Email\Data\UsageWarningEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Notification\Slack\Data\UsageDisable;
use BillaBear\Notification\Slack\Data\UsageWarning;
use BillaBear\Notification\Slack\NotificationSender;
use Brick\Money\Money;
use Parthenon\Notification\EmailSenderInterface;

readonly class WarningNotifier
{
    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private NotificationSender $notificationSender,
    ) {
    }

    public function notifyOfDisable(Customer $customer, UsageLimit $usageLimit, Money $amount): void
    {
        $emailNotification = new UsageDisableEmail($usageLimit, $amount);
        $email = $this->emailBuilder->build($customer, $emailNotification);
        $this->emailSender->send($email);

        $slackNotification = new UsageDisable($customer, $usageLimit, $amount);
        $this->notificationSender->sendNotification($slackNotification);
    }

    public function notifyOfWarning(Customer $customer, UsageLimit $usageLimit, Money $amount): void
    {
        $emailNotification = new UsageWarningEmail($usageLimit, $amount);
        $email = $this->emailBuilder->build($customer, $emailNotification);
        $this->emailSender->send($email);

        $slackNotification = new UsageWarning($customer, $usageLimit, $amount);
        $this->notificationSender->sendNotification($slackNotification);
    }
}

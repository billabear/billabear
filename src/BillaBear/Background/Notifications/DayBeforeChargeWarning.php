<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Notifications;

use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Notification\Email\Data\DayBeforeChargeWarningEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\EmailSenderInterface;

class DayBeforeChargeWarning
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private EmailSenderInterface $sender,
        private EmailBuilder $emailBuilder,
    ) {
    }

    public function execute(): void
    {
        $all = $this->brandSettingsRepository->getAll();
        $enabled = false;
        foreach ($all as $setting) {
            if ($setting->getNotificationSettings()->getSendBeforeChargeWarnings()) {
                $enabled = true;
            }
        }

        if (!$enabled) {
            return;
        }

        $this->getLogger()->info('Starting day before charge warning run');

        $subscriptions = $this->subscriptionRepository->getSubscriptionsExpiringInTwoDays();

        foreach ($subscriptions as $subscription) {
            if (!$subscription->getCustomer()->getBrandSettings()->getNotificationSettings()->getSendBeforeChargeWarnings()) {
                continue;
            }
            /** @var SubscriptionPlan $plan */
            $plan = $subscription->getSubscriptionPlan();
            if ($plan->getIsTrialStandalone() && SubscriptionStatus::TRIAL_ACTIVE === $subscription->getStatus()) {
                // There isn't going to be a next charge.
                continue;
            }

            $this->getLogger()->info(
                'Sending before charge warning email to customer for subscription',
                [
                    'customer_id' => (string) $subscription->getCustomer()->getId(),
                    'subscription_id' => (string) $subscription->getId(),
                ]
            );
            $emailPayload = new DayBeforeChargeWarningEmail($subscription);
            $email = $this->emailBuilder->build($subscription->getCustomer(), $emailPayload);
            $this->sender->send($email);
        }
    }
}

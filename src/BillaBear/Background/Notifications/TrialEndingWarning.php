<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Notifications;

use BillaBear\Notification\Email\Data\TrialEndingWarningEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\Notification\Exception\UnableToSendMessageException;

class TrialEndingWarning
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly EmailSenderInterface $sender,
        private readonly EmailBuilder $emailBuilder,
        private readonly BrandSettingsRepositoryInterface $brandSettingsRepository,
    ) {
    }

    public function execute(): void
    {
        $all = $this->brandSettingsRepository->getAll();
        $enabled = false;
        foreach ($all as $setting) {
            if ($setting->getNotificationSettings()->getSendTrialEndingWarning()) {
                $enabled = true;
            }
        }

        if (!$enabled) {
            return;
        }

        $this->getLogger()->info('Starting trial ending soon warning email run');
        $subscriptions = $this->subscriptionRepository->getTrialEndingInNextSevenDays();

        foreach ($subscriptions as $subscription) {
            if (!$subscription->getCustomer()->getBrandSettings()->getNotificationSettings()->getSendTrialEndingWarning()) {
                continue;
            }

            $this->getLogger()->info(
                'Sending trial warning email to customer for subscription',
                [
                    'customer_id' => (string) $subscription->getCustomer()->getId(),
                    'subscription_id' => (string) $subscription->getId(),
                ]
            );
            $emailPayload = new TrialEndingWarningEmail($subscription);
            $email = $this->emailBuilder->build($subscription->getCustomer(), $emailPayload);
            try {
                $this->sender->send($email);
            } catch (UnableToSendMessageException $e) {
                $this->getLogger()->error('Unable to send trial ending warning email', ['exception_message' => $e->getMessage()]);
            }
        }
    }
}

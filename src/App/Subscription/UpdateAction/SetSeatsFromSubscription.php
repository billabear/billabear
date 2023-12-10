<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Subscription\UpdateAction;

use App\Entity\Customer;
use App\Entity\Subscription;
use App\Entity\SubscriptionSeatModification;
use App\Enum\SubscriptionSeatModificationType;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use App\Repository\SubscriptionSeatModificationRepositoryInterface;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Obol\SubscriptionFactoryInterface;

class SetSeatsFromSubscription
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionSeatModificationRepositoryInterface $subscriptionSeatModificationRepository,
        private ProviderInterface $provider,
        private SubscriptionFactoryInterface $subscriptionFactory,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function setSeats(Subscription $subscription, int $seats): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        $oldSeatCount = $subscription->getSeats();
        $subscription->setSeats($seats);

        $changeValue = $seats - $oldSeatCount;

        $seatModification = new SubscriptionSeatModification();
        $seatModification->setSubscription($subscription);
        $seatModification->setType($changeValue < 0 ? SubscriptionSeatModificationType::REMOVED : SubscriptionSeatModificationType::ADDED);
        $seatModification->setChangeValue(abs($changeValue));
        $seatModification->setCreatedAt(new \DateTime());

        $this->subscriptionSeatModificationRepository->save($seatModification);
        $this->subscriptionRepository->save($subscription);

        /** @var Customer $customer */
        $customer = $subscription->getCustomer();
        if ($settings->getSystemSettings()->isUseStripeBilling() && 'card' === $customer->getBillingType()) {
            $model = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);
            $this->provider->subscriptions()->updateSubscriptionSeats($model);
        }
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\UpdateAction;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionSeatModification;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Repository\SubscriptionSeatModificationRepositoryInterface;
use BillaBear\Subscription\SubscriptionSeatModificationType;
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

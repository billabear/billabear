<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription;

use App\Repository\SettingsRepositoryInterface;
use Obol\Model\Subscription\UpdatePaymentMethod;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class PaymentMethodUpdateProcessor
{
    use LoggerAwareTrait;

    public function __construct(
        private ProviderInterface $provider,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function process(Subscription $subscription, PaymentCard $newPaymentDetails): void
    {
        $subscription->setPaymentDetails($newPaymentDetails);

        foreach ($this->subscriptionRepository->getAllActiveForCustomer($subscription->getCustomer()) as $otherSubscription) {
            if ($subscription->getMainExternalReference() === $otherSubscription->getMainExternalReference()) {
                $otherSubscription->setPaymentDetails($newPaymentDetails);
                $this->subscriptionRepository->save($otherSubscription);
            }
        }
        $this->subscriptionRepository->save($subscription);

        if ($this->settingsRepository->getDefaultSettings()->getSystemSettings()->isUseStripeBilling()) {
            $this->getLogger()->info('Sync with stripe billing');

            $update = new UpdatePaymentMethod();
            $update->setSubscriptionId($subscription->getMainExternalReference());
            $update->setPaymentMethodReference($newPaymentDetails->getStoredPaymentReference());

            $this->provider->subscriptions()->updatePaymentMethod($update);
        }
    }
}

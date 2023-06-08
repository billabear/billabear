<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Provider;

use Obol\Model\Enum\ProrataType;
use Obol\Model\Subscription;
use Obol\Model\Subscription\UpdatePaymentMethod;
use Obol\SubscriptionServiceInterface;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function updatePaymentMethod(UpdatePaymentMethod $updatePaymentMethod): void
    {
        // TODO: Implement updatePaymentMethod() method.
    }

    public function list(int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement list() method.
    }

    public function get(string $id, string $subId): Subscription
    {
        $subscription = new Subscription();
        $subscription->setValidUntil(new \DateTime('+30 days'));

        return $subscription;
    }

    public function updatePrice(Subscription $subscription, ProrataType $prorataType = ProrataType::NONE): void
    {
        // TODO: Implement updatePrice() method.
    }

    public function updateSubscriptionSeats(Subscription $subscription): void
    {
        // TODO: Implement updateSubscriptionSeats() method.
    }
}

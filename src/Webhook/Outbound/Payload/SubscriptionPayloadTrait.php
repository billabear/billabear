<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound\Payload;

use App\Entity\Subscription;

trait SubscriptionPayloadTrait
{
    public function getSubscriptionData(Subscription $subscription): array
    {
        return [
            'id' => (string) $subscription->getId(),
            'plan_name' => $subscription->getPlanName(),
            'status' => $subscription->getStatus()->value,
        ];
    }
}

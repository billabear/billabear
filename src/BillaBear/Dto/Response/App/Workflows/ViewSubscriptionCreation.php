<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Workflows;

use BillaBear\Dto\Generic\App\Workflows\SubscriptionCreation;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewSubscriptionCreation
{
    #[SerializedName('subscription_creation')]
    private SubscriptionCreation $subscriptionCreation;

    public function getSubscriptionCreation(): SubscriptionCreation
    {
        return $this->subscriptionCreation;
    }

    public function setSubscriptionCreation(SubscriptionCreation $subscriptionCreation): void
    {
        $this->subscriptionCreation = $subscriptionCreation;
    }
}

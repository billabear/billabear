<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Notification\Email\Data;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\EmailTemplate;
use Parthenon\Billing\Entity\Subscription;

class SubscriptionCancelEmail extends AbstractEmailData
{
    public function __construct(
        private Subscription $subscription,
    ) {
    }

    public function getData(Customer $customer, BrandSettings $brandSettings): array
    {
        return [
            'brand' => $this->getBrandData($brandSettings),
            'customer' => $this->getCustomerData($customer),
            'subscription' => $this->getSubscriptionData($this->subscription),
        ];
    }

    protected function getSubscriptionData(Subscription $subscription): array
    {
        return [
            'plan_name' => $subscription->getPlanName(),
            'finishes_at' => $subscription->getValidUntil()->format(\DATE_ATOM),
        ];
    }

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_SUBSCRIPTION_CANCELLED;
    }
}

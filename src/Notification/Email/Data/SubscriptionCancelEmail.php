<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

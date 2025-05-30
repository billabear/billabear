<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email\Data;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use Parthenon\Billing\Entity\Subscription;

class SubscriptionCreatedEmailData extends AbstractEmailData
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

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_SUBSCRIPTION_CREATED;
    }

    protected function getSubscriptionData(Subscription $subscription): array
    {
        return [
            'plan_name' => $subscription->getPlanName(),
            'has_trial' => $subscription->isHasTrial(),
            'trial_length' => $subscription->getTrialLengthDays(),
            'payment_schedule' => $subscription->getPaymentSchedule(),
            'amount' => (string) $subscription->getMoneyAmount(),
            'next_payment_due' => $subscription->getValidUntil()->format(\DATE_ATOM),
        ];
    }
}

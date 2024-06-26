<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\Customer;

trait CustomerTrait
{
    protected function buildCustomerData(Customer $customer): array
    {
        return [
            'id' => (string) $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getBillingEmail(),
            'brand' => $customer->getBrandSettings()->getBrandName(),
            'billing_type' => $customer->getBillingType(),
            'customer_type' => $customer->getType()->value,
        ];
    }
}

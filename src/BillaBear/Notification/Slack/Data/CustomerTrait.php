<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound\Payload;

use App\Entity\Customer;

trait CustomerPayloadTrait
{
    protected function getCustomerData(?Customer $customer): ?array
    {
        if (!$customer) {
            return null;
        }

        return [
            'id' => (string) $customer->getId(),
            'email' => $customer->getBillingEmail(),
            'brand' => $customer->getBrand(),
            'is_disabled' => $customer->isDisabled(),
        ];
    }
}

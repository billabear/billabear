<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Parts;

use BillaBear\Entity\Customer;

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

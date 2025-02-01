<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Hubspot;

use BillaBear\Integrations\Crm\CustomerRegistration;
use BillaBear\Integrations\Crm\CustomerServiceInterface;
use Parthenon\Common\LoggerAwareTrait;

class CustomerService implements CustomerServiceInterface
{
    use LoggerAwareTrait;

    public function register(\BillaBear\Entity\Customer $customer): CustomerRegistration
    {
        // TODO: Implement register() method.
    }

    public function update(\BillaBear\Entity\Customer $customer): void
    {
        // TODO: Implement update() method.
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter;

use BillaBear\Entity\Customer;

interface CustomerServiceInterface
{
    public function register(string $listId, Customer $customer): CustomerRegistration;

    public function update(string $listId, Customer $customer): void;
}

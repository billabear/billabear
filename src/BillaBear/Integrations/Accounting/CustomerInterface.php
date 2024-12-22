<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting;

use BillaBear\Entity\Customer;

interface CustomerInterface
{
    public function register(Customer $customer): CustomerRegistration;

    public function update(Customer $customer): void;

    public function delete(Customer $customer): void;

    public function findCustomer(Customer $customer): ?CustomerRegistration;
}

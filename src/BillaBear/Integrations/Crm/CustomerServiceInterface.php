<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm;

use BillaBear\Entity\Customer;

interface CustomerServiceInterface
{
    public function registerCompany(Customer $customer): CustomerRegistration;

    public function registerContact(Customer $customer): ContactRegistration;

    public function update(Customer $customer): void;

    public function search(Customer $customer): ?CustomerProfile;
}

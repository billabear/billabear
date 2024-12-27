<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting;

use BillaBear\Entity\Customer;
use BillaBear\Exception\Integrations\UnexpectedErrorException;

interface CustomerServiceInterface
{
    /**
     * @throws UnexpectedErrorException
     */
    public function register(Customer $customer): CustomerRegistration;

    /**
     * @throws UnexpectedErrorException
     */
    public function update(Customer $customer): void;

    /**
     * @throws UnexpectedErrorException
     */
    public function delete(Customer $customer): void;

    /**
     * @throws UnexpectedErrorException
     */
    public function findCustomer(Customer $customer): ?CustomerRegistration;
}

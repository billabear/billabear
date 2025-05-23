<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Provider;

use Obol\CustomerServiceInterface;
use Obol\Model\Customer;
use Obol\Model\CustomerCreation;

class CustomerService implements CustomerServiceInterface
{
    public function create(Customer $customer): CustomerCreation
    {
        $customerCreation = new CustomerCreation();
        $customerCreation->setReference(bin2hex(random_bytes(32)));
        $customerCreation->setDetailsUrl(null);

        return $customerCreation;
    }

    public function fetch(string $customerId): Customer
    {
        // TODO: Implement fetch() method.
    }

    public function list(int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement list() method.
    }

    public function getCards(string $customerId, int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement getCards() method.
    }

    public function update(Customer $customer): bool
    {
        return true;
    }
}

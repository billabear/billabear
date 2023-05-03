<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dummy\Provider;

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
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\Subscription;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

interface InvoiceRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return Invoice[]
     */
    public function getAllForCustomer(Customer $customer): array;

    /**
     * @return Invoice[]
     */
    public function getOverdueInvoices(): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getLatestForSubscription(Subscription $subscription): Invoice;
}

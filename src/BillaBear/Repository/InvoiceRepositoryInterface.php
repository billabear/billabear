<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\Subscription;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\ResultSet;
use Parthenon\Common\Exception\NoEntityFoundException;

interface InvoiceRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return Invoice[]
     */
    public function getAllForCustomer(Customer $customer): array;

    public function getLastTenForCustomer(Customer $customer): ResultSet;

    public function getLastForCustomer(Customer $customer): ?Invoice;

    /**
     * @return Invoice[]
     */
    public function getOverdueInvoices(): array;

    /**
     * @return Invoice[]
     */
    public function getUnpaidInvoices(): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getLatestForSubscription(Subscription $subscription): Invoice;

    public function getTotalCount(): int;
}

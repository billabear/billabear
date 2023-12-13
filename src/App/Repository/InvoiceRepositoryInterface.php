<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

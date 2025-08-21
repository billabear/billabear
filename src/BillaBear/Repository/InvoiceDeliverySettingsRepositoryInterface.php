<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use BillaBear\Entity\InvoiceDeliverySettings;
use Parthenon\Common\Repository\RepositoryInterface;

interface InvoiceDeliverySettingsRepositoryInterface extends RepositoryInterface
{
    /**
     * @return InvoiceDeliverySettings[]
     */
    public function getAllForCustomer(Customer $customer): array;

    /**
     * @return InvoiceDeliverySettings[]
     */
    public function getEnabledForCustomer(Customer $customer): array;
}

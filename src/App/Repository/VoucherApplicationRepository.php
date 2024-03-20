<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\VoucherApplication;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class VoucherApplicationRepository extends DoctrineRepository implements VoucherApplicationRepositoryInterface
{
    public function findUnUsedForCustomer(Customer $customer): VoucherApplication
    {
        $voucherApplication = $this->entityRepository->findOneBy(['customer' => $customer, 'used' => false]);

        if (!$voucherApplication instanceof VoucherApplication) {
            throw new NoEntityFoundException(sprintf("No voucher application for '%s'", $customer->getBillingEmail()));
        }

        return $voucherApplication;
    }
}

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

namespace App\Credit;

use App\Entity\Credit;
use App\Entity\Customer;
use App\Repository\CreditRepositoryInterface;
use Brick\Money\Money;

class CreditAdjustmentRecorder
{
    public function __construct(
        private CreditRepositoryInterface $creditRepository,
    ) {
    }

    public function createRecord(string $type, Customer $customer, Money $amount)
    {
        $credit = new Credit();
        $credit->setCustomer($customer);
        $credit->setType($type);
        $credit->setAmount($amount->getMinorAmount()->toInt());
        $credit->setCurrency($amount->getCurrency()->getCurrencyCode());
        $credit->setUsedAmount(0);
        $credit->setCreationType(Credit::CREATION_TYPE_AUTOMATED);
        $credit->setCreatedAt(new \DateTime());
        $credit->setUpdatedAt(new \DateTime());

        $this->creditRepository->save($credit);
    }
}

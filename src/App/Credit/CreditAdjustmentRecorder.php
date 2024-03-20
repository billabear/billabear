<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Credit;

use App\Entity\Credit;
use App\Entity\Customer;
use App\Repository\CreditRepositoryInterface;
use Brick\Money\Money;
use Parthenon\Billing\Entity\BillingAdminInterface;

class CreditAdjustmentRecorder
{
    public function __construct(
        private CreditRepositoryInterface $creditRepository,
        private StripeBillingRegister $stripeBillingRegister,
    ) {
    }

    public function createRecord(string $type, Customer $customer, Money $amount, ?string $reason = null, ?BillingAdminInterface $billingAdmin = null): Credit
    {
        $credit = new Credit();
        $credit->setCustomer($customer);
        $credit->setType($type);
        $credit->setAmount($amount->getMinorAmount()->toInt());
        $credit->setCurrency($amount->getCurrency()->getCurrencyCode());
        $credit->setUsedAmount(0);
        $credit->setBillingAdmin($billingAdmin);
        $credit->setReason($reason);
        $credit->setCreationType(null === $billingAdmin ? Credit::CREATION_TYPE_AUTOMATED : Credit::CREATION_TYPE_MANUALLY);
        $credit->setCreatedAt(new \DateTime());
        $credit->setUpdatedAt(new \DateTime());

        $this->stripeBillingRegister->register($credit);

        $this->creditRepository->save($credit);

        return $credit;
    }
}

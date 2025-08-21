<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Credit;

use BillaBear\Entity\Credit;
use BillaBear\Entity\Customer;
use BillaBear\Integrations\Accounting\Messenger\SyncCredit;
use BillaBear\Repository\CreditRepositoryInterface;
use Brick\Money\Money;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreditAdjustmentRecorder
{
    public function __construct(
        private CreditRepositoryInterface $creditRepository,
        private StripeBillingRegister $stripeBillingRegister,
        private MessageBusInterface $messageBus,
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
        $this->messageBus->dispatch(new SyncCredit((string) $credit->getId()));

        return $credit;
    }
}

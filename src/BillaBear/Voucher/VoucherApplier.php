<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Voucher;

use BillaBear\Credit\CreditAdjustmentRecorder;
use BillaBear\Entity\Credit;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Voucher;
use BillaBear\Entity\VoucherApplication;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\VoucherApplicationRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class VoucherApplier
{
    public function __construct(
        private VoucherApplicationRepositoryInterface $voucherApplicationRepository,
        private CreditAdjustmentRecorder $creditAdjustmentRecorder,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function applyVoucherToCustomer(Customer $customer, Voucher $voucher): void
    {
        $now = new \DateTime();
        $voucherApplication = new VoucherApplication();
        $voucherApplication->setVoucher($voucher);
        $voucherApplication->setCustomer($customer);
        $voucherApplication->setCreatedAt($now);

        if (VoucherType::FIXED_CREDIT == $voucher->getType()) {
            // Figure out currency;
            if ($customer->getCreditCurrency()) {
                $currency = $customer->getCreditCurrency();
            } else {
                foreach ($customer->getSubscriptions() as $subscription) {
                    if (!$subscription->isActive() && $subscription->getEndedAt() < $now) {
                        continue;
                    }
                    $currency = $subscription->getCurrency();
                }
                if (!isset($currency)) {
                    $currency = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency();
                }
            }

            $this->creditAdjustmentRecorder->createRecord(Credit::TYPE_CREDIT, $customer, $voucher->getAmountForCurrency($currency)->getAsMoney(), $voucher->getName());
            $voucherApplication->setUsed(true);
        }

        $this->voucherApplicationRepository->save($voucherApplication);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Voucher;

use App\Credit\CreditAdjustmentRecorder;
use App\Entity\Credit;
use App\Entity\Customer;
use App\Entity\Voucher;
use App\Entity\VoucherApplication;
use App\Enum\VoucherType;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\VoucherApplicationRepositoryInterface;

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

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

namespace App\Factory\Reports;

use App\Dto\Response\App\Reports\ExpiringCard;
use App\Factory\CustomerFactory;
use App\Factory\PaymentMethodsFactory;
use Parthenon\Billing\Entity\PaymentCard;

class ExpiringCardsFactory
{
    public function __construct(
        private CustomerFactory $customerFactory,
        private PaymentMethodsFactory $paymentMethodsFactory,
    ) {
    }

    public function createAppDto(PaymentCard $paymentCard): ExpiringCard
    {
        $dto = new ExpiringCard();
        $dto->setCustomer($this->customerFactory->createAppDto($paymentCard->getCustomer()));
        $dto->setPaymentCard($this->paymentMethodsFactory->createAppDto($paymentCard));

        return $dto;
    }
}

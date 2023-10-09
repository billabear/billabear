<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Reports;

use App\DataMappers\CustomerDataMapper;
use App\DataMappers\PaymentMethodsDataMapper;
use App\Dto\Response\App\Reports\ExpiringCard;
use Parthenon\Billing\Entity\PaymentCard;

class ExpiringCardsDataMapper
{
    public function __construct(
        private CustomerDataMapper $customerFactory,
        private PaymentMethodsDataMapper $paymentMethodsFactory,
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

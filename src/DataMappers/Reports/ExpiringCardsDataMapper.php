<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

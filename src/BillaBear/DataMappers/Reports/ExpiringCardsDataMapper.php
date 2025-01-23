<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Reports;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\PaymentMethodsDataMapper;
use BillaBear\Dto\Response\App\Reports\ExpiringCard;
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

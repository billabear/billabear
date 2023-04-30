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

namespace App\Factory;

use App\Dto\Generic\App\ChargeBack as AppDto;
use Parthenon\Billing\Entity\ChargeBack;

class ChargeBackFactory
{
    public function __construct(
        private CustomerFactory $customerFactory,
        private PaymentFactory $paymentFactory,
    ) {
    }

    public function createAppDto(ChargeBack $chargeBack): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $chargeBack->getId());
        $dto->setPayment($this->paymentFactory->createAppDto($chargeBack->getPayment()));
        $dto->setCustomer($this->customerFactory->createAppDto($chargeBack->getCustomer()));
        $dto->setStatus($chargeBack->getStatus()->value);
        $dto->setReason($chargeBack->getReason()->value);
        $dto->setCreatedAt($chargeBack->getCreatedAt());

        return $dto;
    }
}

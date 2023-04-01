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

use App\Dto\Generic\Api\PaymentDetails as ApiDto;
use Parthenon\Billing\Entity\PaymentDetails;

class PaymentDetailsFactory
{
    public function createApiDto(PaymentDetails $paymentDetails): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId($paymentDetails->getId());
        $dto->setName($paymentDetails->getName());
        $dto->setBrand($paymentDetails->getBrand());
        $dto->setExpiryMonth($paymentDetails->getExpiryMonth());
        $dto->setExpiryYear($paymentDetails->getExpiryYear());
        $dto->setLastFour($paymentDetails->getLastFour());
        $dto->setDefault($paymentDetails->isDefaultPaymentOption());

        return $dto;
    }
}

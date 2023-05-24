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

use App\Dto\Generic\App\Invoice as AppDto;
use App\Entity\Invoice as Entity;

class InvoiceFactory
{
    public function __construct(private CustomerFactory $customerFactory)
    {
    }

    public function createAppDto(Entity $invoice): AppDto
    {
        $dto = new AppDto();
        $dto->setCustomer($this->customerFactory->createAppDto($invoice->getCustomer()));
        $dto->setCreatedAt($invoice->getCreatedAt());
        $dto->setAmountDue($invoice->getAmountDue());
        $dto->setCurrency($invoice->getCurrency());
        $dto->setIsPaid($invoice->isPaid());

        return $dto;
    }
}

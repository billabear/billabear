<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\Invoice as AppDto;
use App\Dto\Generic\App\InvoiceLine;
use App\Dto\Generic\App\InvoiceQuickView as AppQuickViewDto;
use App\Entity\Invoice as Entity;

class InvoiceDataMapper
{
    public function __construct(
        private CustomerDataMapper $customerFactory,
        private AddressDataMapper $addressDataMapper,
    ) {
    }

    public function createQuickViewAppDto(Entity $invoice): AppQuickViewDto
    {
        $dto = new AppQuickViewDto();
        $dto->setId((string) $invoice->getId());
        $dto->setCustomer($this->customerFactory->createAppDto($invoice->getCustomer()));
        $dto->setCreatedAt($invoice->getCreatedAt());
        $dto->setAmountDue($invoice->getAmountDue());
        $dto->setCurrency($invoice->getCurrency());
        $dto->setIsPaid($invoice->isPaid());
        $dto->setTotal($invoice->getTotal());

        return $dto;
    }

    public function createAppDto(Entity $invoice): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $invoice->getId());
        $dto->setNumber($invoice->getInvoiceNumber());
        $dto->setCustomer($this->customerFactory->createAppDto($invoice->getCustomer()));
        $dto->setCreatedAt($invoice->getCreatedAt());
        $dto->setAmountDue($invoice->getAmountDue());
        $dto->setPaidAt($invoice->getPaidAt());
        $dto->setCurrency($invoice->getCurrency());
        $dto->setIsPaid($invoice->isPaid());
        $dto->setTaxTotal($invoice->getTaxTotal());
        $dto->setSubTotal($invoice->getSubTotal());
        $dto->setTotal($invoice->getTotal());
        $dto->setBillerAddress($this->addressDataMapper->createDto($invoice->getBillerAddress()));
        $dto->setPayeeAddress($this->addressDataMapper->createDto($invoice->getPayeeAddress()));

        $lines = [];
        foreach ($invoice->getLines() as $line) {
            $lineDto = new InvoiceLine();
            $lineDto->setDescription($line->getDescription());
            $lineDto->setTaxRate($line->getTaxPercentage());
            $lineDto->setCurrency($line->getCurrency());
            $lineDto->setTaxTotal($line->getTaxTotal());
            $lineDto->setSubTotal($line->getSubTotal());
            $lineDto->setTotal($line->getTotal());

            $lines[] = $lineDto;
        }
        $dto->setLines($lines);

        return $dto;
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Checkout\PayLinkGeneratorInterface;
use BillaBear\Dto\Generic\Api\Invoice as ApiDto;
use BillaBear\Dto\Generic\Api\InvoiceLine as ApiInvoiceLine;
use BillaBear\Dto\Generic\App\Invoice as AppDto;
use BillaBear\Dto\Generic\App\InvoiceLine as MainInvoiceLine;
use BillaBear\Dto\Generic\App\InvoiceQuickView as AppQuickViewDto;
use BillaBear\Dto\Response\Portal\Invoice\Invoice as PublicDto;
use BillaBear\Dto\Response\Portal\Invoice\InvoiceLine;
use BillaBear\Entity\Invoice as Entity;

class InvoiceDataMapper
{
    public function __construct(
        private CustomerDataMapper $customerFactory,
        private AddressDataMapper $addressDataMapper,
        private PayLinkGeneratorInterface $payLinkGenerator,
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
        $dto->setPayLink($this->payLinkGenerator->generatePayLink($invoice));
        $dto->setDueDate($invoice->getDueAt());

        $lines = [];
        foreach ($invoice->getLines() as $line) {
            $lineDto = new MainInvoiceLine();
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

    public function createApiDto(Entity $invoice): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $invoice->getId());
        $dto->setNumber($invoice->getInvoiceNumber());
        $dto->setCustomer($this->customerFactory->createApiDto($invoice->getCustomer()));
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
        $dto->setPayLink($this->payLinkGenerator->generatePayLink($invoice));
        $dto->setDueDate($invoice->getDueAt());

        $lines = [];
        foreach ($invoice->getLines() as $line) {
            $lineDto = new ApiInvoiceLine();
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

    public function createPublicDto(Entity $invoice): PublicDto
    {
        $dto = new PublicDto();
        $dto->setAmount($invoice->getAmountDue());
        $dto->setCurrency($invoice->getCurrency());
        $dto->setNumber($invoice->getInvoiceNumber());
        $dto->setBillerAddress($this->addressDataMapper->createDto($invoice->getBillerAddress()));
        $dto->setPayeeAddress($this->addressDataMapper->createDto($invoice->getPayeeAddress()));
        $dto->setCreatedAt($invoice->getCreatedAt());
        $dto->setEmailAddress($invoice->getCustomer()->getBillingEmail());
        $dto->setPaid($invoice->isPaid());

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

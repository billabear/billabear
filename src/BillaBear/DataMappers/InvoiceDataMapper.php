<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Checkout\PayLinkGeneratorInterface;
use BillaBear\Dto\Generic\Api\Invoice as ApiDto;
use BillaBear\Dto\Generic\Api\InvoiceLine as ApiInvoiceLine;
use BillaBear\Dto\Generic\App\Invoice as AppDto;
use BillaBear\Dto\Generic\App\InvoiceLine as MainInvoiceLine;
use BillaBear\Dto\Generic\App\InvoiceQuickView as AppQuickViewDto;
use BillaBear\Dto\Generic\Public\Invoice as PublicDto;
use BillaBear\Dto\Generic\Public\InvoiceLine;
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
        $lines = [];
        foreach ($invoice->getLines() as $line) {
            $lineDto = new MainInvoiceLine(
                $line->getDescription(),
                $line->getCurrency(),
                $line->getTotal(),
                $line->getSubTotal(),
                $line->getTaxTotal(),
                $line->getTaxPercentage(),
            );

            $lines[] = $lineDto;
        }

        $dto = new AppDto(
            (string) $invoice->getId(),
            $invoice->getInvoiceNumber(),
            $invoice->getCurrency(),
            $invoice->getTotal(),
            $invoice->getTaxTotal(),
            $invoice->getSubTotal(),
            $invoice->getAmountDue(),
            $invoice->isPaid(),
            $invoice->getPaidAt(),
            $invoice->getCreatedAt(),
            $this->customerFactory->createAppDto($invoice->getCustomer()),
            $this->addressDataMapper->createDto($invoice->getBillerAddress()),
            $this->addressDataMapper->createDto($invoice->getPayeeAddress()),
            $lines,
            $this->payLinkGenerator->generatePayLink($invoice),
            $invoice->getDueAt()
        );

        return $dto;
    }

    public function createApiDto(Entity $invoice): ApiDto
    {
        $lines = [];
        foreach ($invoice->getLines() as $line) {
            $lineDto = new ApiInvoiceLine(
                $line->getDescription(),
                $line->getCurrency(),
                $line->getTotal(),
                $line->getSubTotal(),
                $line->getTaxTotal(),
                $line->getTaxPercentage(),
            );

            $lines[] = $lineDto;
        }
        $dto = new ApiDto(
            (string) $invoice->getId(),
            $invoice->getInvoiceNumber(),
            $invoice->getCurrency(),
            $invoice->getTotal(),
            $invoice->getTaxTotal(),
            $invoice->getSubTotal(),
            $invoice->getAmountDue(),
            $invoice->isPaid(),
            $invoice->getPaidAt(),
            $invoice->getCreatedAt(),
            $this->customerFactory->createApiDto($invoice->getCustomer()),
            $this->addressDataMapper->createDto($invoice->getBillerAddress()),
            $this->addressDataMapper->createDto($invoice->getPayeeAddress()),
            $lines,
            $this->payLinkGenerator->generatePayLink($invoice),
            $invoice->getDueAt()
        );

        return $dto;
    }

    public function createPublicDto(Entity $invoice): PublicDto
    {
        $lines = [];
        foreach ($invoice->getLines() as $line) {
            $lines[] = new InvoiceLine(
                $line->getDescription(),
                $line->getCurrency(),
                $line->getTotal(),
                $line->getTaxTotal(),
                $line->getSubTotal(),
                $line->getTaxPercentage(),
            );
        }

        return new PublicDto(
            (string) $invoice->getId(),
            $invoice->getInvoiceNumber(),
            $invoice->getCurrency(),
            $invoice->getAmountDue(),
            $invoice->isPaid(),
            $invoice->getCreatedAt(),
            $this->addressDataMapper->createDto($invoice->getBillerAddress()),
            $this->addressDataMapper->createDto($invoice->getPayeeAddress()),
            $invoice->getCustomer()->getBillingEmail(),
            $lines,
            $invoice->getPaidAt(),
        );
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Invoice\InvoiceDeliveryStatus;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table('invoice_delivery')]
class InvoiceDelivery
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Invoice::class)]
    private Invoice $invoice;

    #[ORM\ManyToOne(targetEntity: InvoiceDeliverySettings::class)]
    private InvoiceDeliverySettings $invoiceDeliverySettings;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\Column(type: 'string', length: 255, enumType: InvoiceDeliveryStatus::class)]
    private InvoiceDeliveryStatus $status;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getInvoiceDeliverySettings(): InvoiceDeliverySettings
    {
        return $this->invoiceDeliverySettings;
    }

    public function setInvoiceDeliverySettings(InvoiceDeliverySettings $invoiceDeliverySettings): void
    {
        $this->invoiceDeliverySettings = $invoiceDeliverySettings;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getStatus(): InvoiceDeliveryStatus
    {
        return $this->status;
    }

    public function setStatus(InvoiceDeliveryStatus $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

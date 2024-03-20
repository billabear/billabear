<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\App;

use App\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Invoice
{
    private string $id;

    private string $number;

    private string $currency;

    private int $total;

    #[SerializedName('tax_total')]
    private int $taxTotal;

    #[SerializedName('sub_total')]
    private int $subTotal;

    #[SerializedName('amount_due')]
    private int $amountDue;

    #[SerializedName('is_paid')]
    private bool $isPaid;

    #[SerializedName('paid_at')]
    private ?\DateTimeInterface $paidAt;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    private Customer $customer;

    #[SerializedName('biller_address')]
    private Address $billerAddress;

    #[SerializedName('payee_address')]
    private Address $payeeAddress;

    private array $lines;

    #[SerializedName('pay_link')]
    private string $payLink;

    #[SerializedName('due_date')]
    private ?\DateTime $dueDate;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmountDue(): int
    {
        return $this->amountDue;
    }

    public function setAmountDue(int $amountDue): void
    {
        $this->amountDue = $amountDue;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): void
    {
        $this->isPaid = $isPaid;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getTaxTotal(): int
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function getSubTotal(): int
    {
        return $this->subTotal;
    }

    public function setSubTotal(int $subTotal): void
    {
        $this->subTotal = $subTotal;
    }

    public function getBillerAddress(): Address
    {
        return $this->billerAddress;
    }

    public function setBillerAddress(Address $billerAddress): void
    {
        $this->billerAddress = $billerAddress;
    }

    public function getPayeeAddress(): Address
    {
        return $this->payeeAddress;
    }

    public function setPayeeAddress(Address $payeeAddress): void
    {
        $this->payeeAddress = $payeeAddress;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function setLines(array $lines): void
    {
        $this->lines = $lines;
    }

    public function setPaidAt(?\DateTimeInterface $getPaidAt)
    {
        $this->paidAt = $getPaidAt;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    public function getPayLink(): string
    {
        return $this->payLink;
    }

    public function setPayLink(string $payLink): void
    {
        $this->payLink = $payLink;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }
}

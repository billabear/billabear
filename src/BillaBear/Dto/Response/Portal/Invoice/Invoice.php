<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\Portal\Invoice;

use BillaBear\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Invoice
{
    protected string $number;

    protected string $currency;

    protected int $amount;

    protected bool $paid;

    #[SerializedName('created_at')]
    protected \DateTime $createdAt;
    #[SerializedName('biller_address')]
    private ?Address $billerAddress = null;

    #[SerializedName('payee_address')]
    private ?Address $payeeAddress = null;

    #[SerializedName('email_address')]
    private string $emailAddress;

    private array $lines = [];

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }

    public function getBillerAddress(): Address
    {
        return $this->billerAddress;
    }

    public function setBillerAddress(Address $billerAddress): void
    {
        $this->billerAddress = $billerAddress;
    }

    public function getPayeeAddress(): ?Address
    {
        return $this->payeeAddress;
    }

    public function setPayeeAddress(?Address $payeeAddress): void
    {
        $this->payeeAddress = $payeeAddress;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function setLines(array $lines): void
    {
        $this->lines = $lines;
    }
}

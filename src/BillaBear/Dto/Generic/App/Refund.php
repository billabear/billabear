<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Refund
{
    private string $id;

    private int $amount;

    private string $currency;

    private Customer $customer;

    private Payment $payment;

    #[SerializedName('billing_admin')]
    private ?BillingAdmin $billingAdmin = null;

    private string $status;

    private ?string $reason = null;

    #[SerializedName('external_reference')]
    private string $externalReference;

    #[SerializedName('created_at')]
    private \DateTimeInterface $createdAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    public function getBillingAdmin(): ?BillingAdmin
    {
        return $this->billingAdmin;
    }

    public function setBillingAdmin(?BillingAdmin $billingAdmin): void
    {
        $this->billingAdmin = $billingAdmin;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getExternalReference(): string
    {
        return $this->externalReference;
    }

    public function setExternalReference(string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

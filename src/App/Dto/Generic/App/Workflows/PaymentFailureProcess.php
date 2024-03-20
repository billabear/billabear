<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\App\Workflows;

use App\Dto\Generic\App\Customer;
use App\Dto\Generic\App\PaymentAttempt;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PaymentFailureProcess
{
    private string $id;

    #[SerializedName('payment_attempt')]
    private PaymentAttempt $paymentAttempt;

    private Customer $customer;

    private string $state;

    #[SerializedName('retry_country')]
    private int $retryCount;

    private bool $resolved;

    #[SerializedName('next_attempt_at')]
    private \DateTime $nextAttemptAt;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    #[SerializedName('updated_at')]
    private \DateTime $updatedAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getPaymentAttempt(): PaymentAttempt
    {
        return $this->paymentAttempt;
    }

    public function setPaymentAttempt(PaymentAttempt $paymentAttempt): void
    {
        $this->paymentAttempt = $paymentAttempt;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function setRetryCount(int $retryCount): void
    {
        $this->retryCount = $retryCount;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function setResolved(bool $resolved): void
    {
        $this->resolved = $resolved;
    }

    public function getNextAttemptAt(): \DateTime
    {
        return $this->nextAttemptAt;
    }

    public function setNextAttemptAt(\DateTime $nextAttemptAt): void
    {
        $this->nextAttemptAt = $nextAttemptAt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}

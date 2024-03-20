<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\App\Workflows;

use App\Dto\Generic\App\Refund;

class RefundCreatedProcess
{
    private string $id;

    private string $state;

    private Refund $refund;

    private \DateTime $createdAt;

    private \DateTime $updatedAt;

    private ?string $error;

    private bool $hasError;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getRefund(): Refund
    {
        return $this->refund;
    }

    public function setRefund(Refund $refund): void
    {
        $this->refund = $refund;
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

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    public function isHasError(): bool
    {
        return $this->hasError;
    }

    public function setHasError(bool $hasError): void
    {
        $this->hasError = $hasError;
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\App;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CancellationRequest
{
    private string $id;

    private Subscription $subscription;

    private string $when;

    #[SerializedName('refund_type')]
    private string $refundType;

    private string $state;

    private ?string $comment = null;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    #[SerializedName('original_valid_until')]
    private \DateTime $originalValidUntil;

    #[SerializedName('specific_date')]
    private ?\DateTime $specificDate = null;

    private ?string $error;

    #[SerializedName('has_error')]
    private bool $hasError;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getWhen(): string
    {
        return $this->when;
    }

    public function setWhen(string $when): void
    {
        $this->when = $when;
    }

    public function getRefundType(): string
    {
        return $this->refundType;
    }

    public function setRefundType(string $refundType): void
    {
        $this->refundType = $refundType;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getOriginalValidUntil(): \DateTime
    {
        return $this->originalValidUntil;
    }

    public function setOriginalValidUntil(\DateTime $originalValidUntil): void
    {
        $this->originalValidUntil = $originalValidUntil;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getSpecificDate(): ?\DateTime
    {
        return $this->specificDate;
    }

    public function setSpecificDate(?\DateTime $specificDate): void
    {
        $this->specificDate = $specificDate;
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

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Developer\Webhook;

use Symfony\Component\Serializer\Annotation\SerializedName;

class WebhookEventResponse
{
    private $id;

    private ?string $url;

    #[SerializedName('status_code')]
    private ?int $statusCode;

    private ?string $body;

    #[SerializedName('error_message')]
    private ?string $errorMessage;

    #[SerializedName('processing_time')]
    private float $processingTime;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(?int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    public function getProcessingTime(): float
    {
        return $this->processingTime;
    }

    public function setProcessingTime(float $processingTime): void
    {
        $this->processingTime = $processingTime;
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

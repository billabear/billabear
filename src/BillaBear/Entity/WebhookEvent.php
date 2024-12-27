<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Webhook\Outbound\WebhookEventType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'webhook_event')]
class WebhookEvent
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(enumType: WebhookEventType::class)]
    private WebhookEventType $type;

    #[ORM\Column(type: 'text')]
    private string $payload;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\OneToMany(targetEntity: WebhookEventResponse::class, mappedBy: 'event', cascade: ['persist'])]
    private array|Collection $responses;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getType(): WebhookEventType
    {
        return $this->type;
    }

    public function setType(WebhookEventType $type): void
    {
        $this->type = $type;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): void
    {
        $this->payload = $payload;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getResponses(): array|Collection
    {
        return $this->responses;
    }

    public function setResponses(array|Collection $responses): void
    {
        $this->responses = $responses;
    }
}

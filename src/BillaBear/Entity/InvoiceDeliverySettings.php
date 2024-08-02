<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Enum\InvoiceDeliveryType;
use BillaBear\Enum\InvoiceFormat;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity()]
#[ORM\Table('invoice_delivery_settings')]
class InvoiceDeliverySettings
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\Column(type: 'string', length: 255)]
    private bool $enabled;

    #[ORM\Column(type: 'string', length: 255, enumType: InvoiceFormat::class)]
    private InvoiceFormat $invoiceFormat;

    #[ORM\Column(type: 'string', length: 255, enumType: InvoiceDeliveryType::class)]
    private InvoiceDeliveryType $type;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $webhookMethod = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $webhookUrl = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $sftpHost = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $sftpUser = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $sftpPassword = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $sftpDir = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sftpPort = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getType(): InvoiceDeliveryType
    {
        return $this->type;
    }

    public function setType(InvoiceDeliveryType $type): void
    {
        $this->type = $type;
    }

    public function getWebhookMethod(): ?string
    {
        return $this->webhookMethod;
    }

    public function setWebhookMethod(?string $webhookMethod): void
    {
        $this->webhookMethod = $webhookMethod;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(?string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getSftpHost(): ?string
    {
        return $this->sftpHost;
    }

    public function setSftpHost(?string $sftpHost): void
    {
        $this->sftpHost = $sftpHost;
    }

    public function getSftpDir(): ?string
    {
        return $this->sftpDir;
    }

    public function setSftpDir(?string $sftpDir): void
    {
        $this->sftpDir = $sftpDir;
    }

    public function getSftpPort(): ?int
    {
        return $this->sftpPort;
    }

    public function setSftpPort(?int $sftpPort): void
    {
        $this->sftpPort = $sftpPort;
    }

    public function getSftpUser(): ?string
    {
        return $this->sftpUser;
    }

    public function setSftpUser(?string $sftpUser): void
    {
        $this->sftpUser = $sftpUser;
    }

    public function getSftpPassword(): ?string
    {
        return $this->sftpPassword;
    }

    public function setSftpPassword(?string $sftpPassword): void
    {
        $this->sftpPassword = $sftpPassword;
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

    public function getInvoiceFormat(): InvoiceFormat
    {
        return $this->invoiceFormat;
    }

    public function setInvoiceFormat(InvoiceFormat $invoiceFormat): void
    {
        $this->invoiceFormat = $invoiceFormat;
    }
}

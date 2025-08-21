<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Attribute\SerializedName;

class InvoiceDelivery
{
    private string $id;

    private string $status;

    #[SerializedName('invoice_delivery_settings')]
    private InvoiceDeliverySettings $invoiceDeliverySettings;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getInvoiceDeliverySettings(): InvoiceDeliverySettings
    {
        return $this->invoiceDeliverySettings;
    }

    public function setInvoiceDeliverySettings(InvoiceDeliverySettings $invoiceDeliverySettings): void
    {
        $this->invoiceDeliverySettings = $invoiceDeliverySettings;
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

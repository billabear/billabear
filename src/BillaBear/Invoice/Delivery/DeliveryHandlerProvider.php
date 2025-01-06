<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery;

use BillaBear\Entity\InvoiceDeliverySettings;
use BillaBear\Invoice\InvoiceDeliveryType;

class DeliveryHandlerProvider
{
    public function __construct(
        private SftpDeliveryHandler $sftpDeliveryHandler,
        private EmailDeliveryHandler $emailDeliveryHandler,
        private WebhookDeliveryHandler $webhookDeliveryHandler,
    ) {
    }

    public function getDeliveryHandler(InvoiceDeliverySettings $invoiceDelivery): DeliveryHandlerInterface
    {
        return match ($invoiceDelivery->getType()) {
            InvoiceDeliveryType::SFTP => $this->sftpDeliveryHandler,
            InvoiceDeliveryType::WEBHOOK => $this->webhookDeliveryHandler,
            InvoiceDeliveryType::EMAIL => $this->emailDeliveryHandler,
            default => $this->emailDeliveryHandler,
        };
    }
}

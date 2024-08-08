<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Invoice;

use BillaBear\Dto\Generic\App\InvoiceDelivery as AppDto;
use BillaBear\Dto\Request\App\Invoice\CreateInvoiceDelivery;
use BillaBear\Entity\InvoiceDeliverySettings as Entity;
use BillaBear\Enum\InvoiceDeliveryType;
use BillaBear\Enum\InvoiceFormat;

class InvoiceDeliveryDataMapper
{
    public function createEntity(CreateInvoiceDelivery $createInvoiceDelivery, ?Entity $invoiceDelivery = null): Entity
    {
        if (!$invoiceDelivery instanceof Entity) {
            $invoiceDelivery = new Entity();
            $invoiceDelivery->setCreatedAt(new \DateTime());
            $invoiceDelivery->setEnabled(true);
        }
        $invoiceDelivery->setUpdatedAt(new \DateTime());

        $type = InvoiceDeliveryType::from($createInvoiceDelivery->getType());
        $format = InvoiceFormat::from($createInvoiceDelivery->getFormat());

        $invoiceDelivery->setType($type);
        $invoiceDelivery->setInvoiceFormat($format);
        $invoiceDelivery->setWebhookUrl($createInvoiceDelivery->getWebhookUrl());
        $invoiceDelivery->setWebhookMethod($createInvoiceDelivery->getWebhookMethod());
        $invoiceDelivery->setSftpHost($createInvoiceDelivery->getSftpHost());
        $invoiceDelivery->setSftpUser($createInvoiceDelivery->getSftpUser());
        $invoiceDelivery->setSftpPassword($createInvoiceDelivery->getSftpPassword());
        $invoiceDelivery->setSftpPort($createInvoiceDelivery->getSftpPort());
        $invoiceDelivery->setSftpDir($createInvoiceDelivery->getSftpDir());
        $invoiceDelivery->setEmail($createInvoiceDelivery->getEmail());

        return $invoiceDelivery;
    }

    public function createAppDto(Entity $delivery)
    {
        $appDto = new AppDto();
        $appDto->setId($delivery->getId());
        $appDto->setType($delivery->getType()->value);
        $appDto->setFormat($delivery->getInvoiceFormat()->value);
        $appDto->setWebhookUrl($delivery->getWebhookUrl());
        $appDto->setWebhookMethod($delivery->getWebhookMethod());
        $appDto->setSftpHost($delivery->getSftpHost());
        $appDto->setSftpUser($delivery->getSftpUser());
        $appDto->setSftpPassword($delivery->getSftpPassword());
        $appDto->setSftpPort($delivery->getSftpPort());
        $appDto->setSftpDir($delivery->getSftpDir());
        $appDto->setEmail($delivery->getEmail());

        return $appDto;
    }
}

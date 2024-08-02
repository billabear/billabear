<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery\Messenger;

use BillaBear\Invoice\Delivery\DeliveryHandlerProvider;
use BillaBear\Repository\InvoiceDeliveryRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InvoiceDeliveryRequestHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceDeliveryRepositoryInterface $invoiceDeliveryRepository,
        private DeliveryHandlerProvider $deliveryHandlerProvider,
    ) {
    }

    public function __invoke(InvoiceDeliveryRequest $invoiceDeliveryRequest): void
    {
        $this->getLogger()->info(
            'Handling invoice delivery request',
            [
                'invoice_id' => $invoiceDeliveryRequest->invoiceId,
                'invoice_delivery_id' => $invoiceDeliveryRequest->invoiceDeliveryId,
            ]
        );

        $invoice = $this->invoiceRepository->findById($invoiceDeliveryRequest->invoiceId);
        $invoiceDelivery = $this->invoiceDeliveryRepository->findById($invoiceDeliveryRequest->invoiceDeliveryId);

        $handler = $this->deliveryHandlerProvider->getDeliveryHandler($invoiceDelivery);
        $handler->deliver($invoice, $invoiceDelivery);
    }
}

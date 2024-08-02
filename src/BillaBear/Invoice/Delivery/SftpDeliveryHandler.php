<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceDeliverySettings;
use BillaBear\Invoice\Delivery\Factory\SftpTransportFactory;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use Parthenon\Common\LoggerAwareTrait;

class SftpDeliveryHandler implements DeliveryHandlerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SftpTransportFactory $sftpTransportFactory,
        private InvoiceFormatterProvider $invoiceFormatterProvider,
    ) {
    }

    public function deliver(Invoice $invoice, InvoiceDeliverySettings $invoiceDelivery): void
    {
        $this->getLogger()->info('Delivering an invoice via SFTP', ['invoice_id' => (string) $invoice->getId(), 'invoice_delivery_id' => (string) $invoiceDelivery->getId()]);
        $transport = $this->sftpTransportFactory->buildTransport($invoiceDelivery);

        $formatter = $this->invoiceFormatterProvider->getFormatterByType($invoiceDelivery->getInvoiceFormat());
        $transport->write($formatter->filename($invoice), $formatter->generate($invoice));
    }
}

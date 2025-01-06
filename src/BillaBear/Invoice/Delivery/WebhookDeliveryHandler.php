<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceDeliverySettings;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use GuzzleHttp\Exception\BadResponseException;
use Parthenon\Common\Http\ClientInterface;
use Parthenon\Common\LoggerAwareTrait;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class WebhookDeliveryHandler implements DeliveryHandlerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private InvoiceFormatterProvider $invoiceFormatterProvider,
    ) {
    }

    public function deliver(Invoice $invoice, InvoiceDeliverySettings $invoiceDelivery): void
    {
        $formatter = $this->invoiceFormatterProvider->getFormatterByType($invoiceDelivery->getInvoiceFormat());
        $invoiceContents = $formatter->generate($invoice);
        $request = $this->requestFactory->createRequest($invoiceDelivery->getWebhookMethod(), $invoiceDelivery->getWebhookUrl());
        $stream = $this->streamFactory->createStream($invoiceContents);
        $request = $request->withBody($stream);

        try {
            $this->client->sendRequest($request);
        } catch (BadResponseException $exception) {
            $this->getLogger()->warning('Unable to send webhook request for delivery');
            throw $exception;
        }
    }
}

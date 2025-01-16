<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\EasyBill;

use BillaBear\Entity\Invoice;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Accounting\InvoiceRegistration;
use BillaBear\Integrations\Accounting\InvoiceServiceInterface;
use easybill\SDK\Client;
use Parthenon\Common\LoggerAwareTrait;

class InvoiceService implements InvoiceServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(private Client $client)
    {
    }

    public function register(Invoice $invoice): InvoiceRegistration
    {
        $body = $this->buildInvocieData($invoice);
        $this->getLogger()->info('Registering invoice with EasyBill', ['invoice_id' => (string) $invoice->getId(), 'body' => $body]);
        try {
            $response = $this->client->request('POST', 'documents', $body);
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to register invoice to EasyBill', ['invoice_id' => (string) $invoice->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to register invoice to EasyBill', previous: $e);
        }

        $this->client->request('PUT', 'documents/'.$response['id'].'/done');
        $this->getLogger()->info('Invoice registered to EasyBill', ['invoice_id' => (string) $invoice->getId()]);

        return new InvoiceRegistration((string) $response['id']);
    }

    public function update(Invoice $invoice): void
    {
        $body = $this->buildInvocieData($invoice);
        $this->getLogger()->info('Updating invoice in EasyBill', ['invoice_id' => (string) $invoice->getId(), 'body' => $body]);
        try {
            $response = $this->client->request('PUT', 'documents/'.$invoice->getAccountingReference(), $body);
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to update invoice to EasyBill', ['invoice_id' => (string) $invoice->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to update invoice to EasyBill', previous: $e);
        }
        $this->client->request('PUT', 'documents/'.$invoice->getAccountingReference().'/done');

        $this->getLogger()->info('Invoice Updated to EasyBill', ['invoice_id' => (string) $invoice->getId()]);
    }

    public function isPaid(Invoice $invoice): bool
    {
        $response = $this->client->request('GET', 'documents/'.$invoice->getAccountingReference());

        return null !== $response['paid_at'];
    }

    private function buildInvocieData(Invoice $invoice): array
    {
        $lines = [];
        $taxCountry = null;
        foreach ($invoice->getLines() as $line) {
            $taxCountry = $line->getTaxCountry();
            $lines[] = [
                'description' => $line->getDescription(),
                'quantity' => $line->getQuantity(),
                'single_price_net' => $line->getNetPrice(),
                'total_gross_price' => $line->getTotal(),
                'total_vat' => $line->getTaxTotal(),
                'vat_percent' => $line->getTaxPercentage(),
            ];
        }

        return [
            'type' => 'INVOICE',
            'order_number' => $invoice->getInvoiceNumber(),
            'vat_country' => $taxCountry,
            'vat_id' => $invoice->getCustomer()->getTaxNumber(),
            'currency' => $invoice->getCurrency(),
            'customer_id' => $invoice->getCustomer()->getAccountingReference(),
            'created_at' => $invoice->getCreatedAt()->format('Y-m-d H:i:s'),
            'calc_vat_from' => 0,
            'due_date' => !$invoice->isPaid() ? $invoice->getDueAt()?->format('Y-m-d') : null,
            'items' => $lines,
            'is_draft' => false,
            'paid_at' => $invoice->getPaidAt()?->format('Y-m-d H:i:s'),
            'status' => $invoice->isPaid() ? 'DONE' : 'ACCEPT',
        ];
    }
}

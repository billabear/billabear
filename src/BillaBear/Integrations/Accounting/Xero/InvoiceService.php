<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Accounting\InvoiceInterface;
use BillaBear\Integrations\Accounting\InvoiceRegistration;
use GuzzleHttp\ClientInterface;
use Parthenon\Common\LoggerAwareTrait;
use XeroAPI\XeroPHP\Api\AccountingApi;
use XeroAPI\XeroPHP\ApiException;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Models\Accounting\Contact;
use XeroAPI\XeroPHP\Models\Accounting\Invoice as XeroInvoice;
use XeroAPI\XeroPHP\Models\Accounting\Invoices;
use XeroAPI\XeroPHP\Models\Accounting\LineItem;

class InvoiceService implements InvoiceInterface
{
    use LoggerAwareTrait;

    private AccountingApi $accountingApi;

    public function __construct(
        private string $tenantId,
        Configuration $config,
        ClientInterface $client,
    ) {
        $this->accountingApi = new AccountingApi($client, $config);
    }

    public function register(Invoice $invoice): InvoiceRegistration
    {
        $this->getLogger()->info('Registering invoice to xero', ['tenant_id' => $this->tenantId, 'invoice_id' => (string) $invoice->getId()]);
        $invoices = $this->buildInvoice($invoice);

        try {
            $output = $this->accountingApi->createInvoices($this->tenantId, $invoices, true);
        } catch (ApiException $e) {
            $this->logger->error('Failed to create invoice to xero with api error response', ['exception_message' => $e->getResponseBody()]);
            throw new UnexpectedErrorException($e->getMessage(), previous: $e);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create invoice to xero', ['exception_message' => $e->getMessage()]);
            throw new UnexpectedErrorException($e->getMessage(), previous: $e);
        }
        /** @var XeroInvoice $invoice */
        $invoiceData = $output->getInvoices()[0];
        if ($invoiceData->getHasErrors()) {
            $this->logger->error('Failed to create contact to xero due to validation errors', ['tenant_id' => $this->tenantId, 'invoice_id' => (string) $invoice->getId(), 'validation_errors' => $invoiceData->getValidationErrors()]);
            throw new UnexpectedErrorException('Failed to create invoice to xero due to validation errors');
        }
        $id = $invoiceData->getInvoiceId();

        $this->getLogger()->info('Invoice registered to xero', ['tenant_id' => $this->tenantId, 'invoice_id' => (string) $invoice->getId(), 'accounting_reference' => $id]);

        return new InvoiceRegistration($id);
    }

    public function update(Invoice $invoice): void
    {
        $this->getLogger()->info('Updating invoice in xero', ['tenant_id' => $this->tenantId, 'invoice_id' => (string) $invoice->getId()]);
        $invoices = $this->buildInvoice($invoice);
        try {
            $this->accountingApi->updateInvoice($this->tenantId, $invoice->getAccountingReference(), $invoices);
        } catch (ApiException $e) {
            $this->logger->error('Failed to update invoice to xero with api error response', ['exception_message' => $e->getResponseBody()]);
            throw new UnexpectedErrorException($e->getMessage(), previous: $e);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update invoice to xero', ['exception_message' => $e->getMessage()]);
            throw new UnexpectedErrorException($e->getMessage(), previous: $e);
        }
        $this->getLogger()->info('Invoice updated in xero', ['invoice_id' => (string) $invoice->getId(), 'accounting_reference' => $invoice->getAccountingReference()]);
    }

    public function isPaid(Invoice $invoice): bool
    {
        $invoiceData = $this->accountingApi->getInvoice($this->tenantId, $invoice->getAccountingReference());

        return XeroInvoice::STATUS_PAID === $invoiceData->getInvoices()[0]->getStatus();
    }

    private function buildInvoice(Invoice $invoice): Invoices
    {
        $xeroInvoice = new XeroInvoice();

        if ($invoice->getAccountingReference()) {
            $xeroInvoice->setInvoiceID($invoice->getAccountingReference());
        }

        if (!$invoice->getCustomer()->getAccountingReference()) {
            throw new UnexpectedErrorException('Customer does not have an accounting reference');
        }

        $contact = new Contact();
        $contact->setContactId($invoice->getCustomer()->getAccountingReference());
        $contact->setIsCustomer(true);
        $contact->setContactStatus(Contact::CONTACT_STATUS_ACTIVE);
        $contact->setName($invoice->getCustomer()->getBillingAddress()->getCompanyName() ?? $invoice->getCustomer()->getName() ?? $invoice->getCustomer()->getBillingEmail());

        $xeroInvoice->setContact($contact);
        $xeroInvoice->setInvoiceNumber($invoice->getInvoiceNumber());
        $xeroInvoice->setType('ACCREC');
        $xeroInvoice->setDate($invoice->getCreatedAt()->format('Y-m-d'));
        $xeroInvoice->setDueDate($invoice->getDueAt()->format('Y-m-d'));

        $xeroInvoice->setReference(sprintf('%s Invoice %s', $invoice->getCustomer()->getBillingEmail(), $invoice->getInvoiceNumber()));
        $xeroInvoice->setStatus(XeroInvoice::STATUS_AUTHORISED);

        $lines = [];
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            $lineItem = new LineItem();
            $lineItem->setDescription($line->getDescription());
            $lineItem->setQuantity($line->getQuantity());
            $lineItem->setUnitAmount((string) $line->getNetPriceAsMoney()->getAmount());
            $lineItem->setTaxType('OUTPUT');
            $lineItem->setAccountCode('200');
            $lineItem->setTaxAmount((string) $line->getTaxTotalAsMoney()->getAmount());
            $lines[] = $lineItem;
        }
        $xeroInvoice->setLineItems($lines);

        $invoices = new Invoices();
        $invoices->setInvoices([$xeroInvoice]);

        return $invoices;
    }
}

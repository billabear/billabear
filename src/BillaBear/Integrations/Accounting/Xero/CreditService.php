<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Entity\Credit;
use BillaBear\Entity\Refund;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\Accounting\CreditRegistration;
use BillaBear\Integrations\Accounting\CreditServiceInterface;
use BillaBear\Integrations\Accounting\RefundRegistration;
use GuzzleHttp\ClientInterface;
use Parthenon\Common\LoggerAwareTrait;
use XeroAPI\XeroPHP\Api\AccountingApi;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Models\Accounting\Account;
use XeroAPI\XeroPHP\Models\Accounting\Contact;
use XeroAPI\XeroPHP\Models\Accounting\CreditNote;
use XeroAPI\XeroPHP\Models\Accounting\CreditNotes;
use XeroAPI\XeroPHP\Models\Accounting\LineItem;
use XeroAPI\XeroPHP\Models\Accounting\Payment;
use XeroAPI\XeroPHP\Models\Accounting\Payments;

class CreditService implements CreditServiceInterface
{
    use LoggerAwareTrait;

    private AccountingApi $accountingApi;

    public function __construct(
        private string $tenantId,
        private string $accountCode,
        Configuration $config,
        ClientInterface $client,
    ) {
        $this->accountingApi = new AccountingApi($client, $config);
    }

    public function registerRefund(Refund $refund): RefundRegistration
    {
        $this->logger->info('Registering refund with Xero', ['tenant_id' => $this->tenantId, 'refund_id' => (string) $refund->getId()]);

        $creditNotes = $this->buildCreditNotesFromRefund($refund);

        $creditNote = $this->createCreditNote($creditNotes);

        $account = new Account();
        $account->setCode($this->accountCode);

        $payment = new Payment();
        $payment->setDate($refund->getCreatedAt()->format('Y-m-d'));
        $payment->setAmount((string) $refund->getAsMoney()->getAmount());
        $payment->setAccount($account);
        $payment->setReference('Refund for Credit Note '.$creditNote->getCreditNoteID());
        $payment->setCreditNote($creditNote);

        $payments = new Payments();
        $payments->setPayments([$payment]);

        try {
            $refundData = $this->accountingApi->createPayments($this->tenantId, $payments);
        } catch (\Exception $e) {
            $this->getLogger()->error('Error while creating refund payment in xero', ['exception_message' => $e->getMessage()]);

            throw new UnexpectedErrorException('Error creating refund payment in xero', previous: $e);
        }

        $refundDto = $refundData->getPayments()[0];

        if ($refundDto->getHasValidationErrors()) {
            $this->logger->error(
                'Failed to create payment to xero due to validation errors',
                [
                    'tenant_id' => $this->tenantId,
                    'refund_id' => (string) $refund->getId(),
                    'validation_errors' => array_map(fn ($e) => $e->getMessage(), $refundDto->getValidationErrors()),
                ]
            );
            throw new UnexpectedErrorException('Failed to create payment to xero due to validation errors');
        }
        $this->logger->info('Refund registered with Xero', ['tenant_id' => $this->tenantId, 'refund_id' => (string) $refund->getId()]);

        return new RefundRegistration($refundDto->getPaymentID());
    }

    public function registeredCreditNote(Credit $credit): CreditRegistration
    {
        if (Credit::TYPE_CREDIT !== $credit->getType()) {
            throw new UnsupportedFeatureException('Credit type debit is not supported');
        }

        $this->logger->info('Registering credit note with Xero', ['tenant_id' => $this->tenantId, 'credit_id' => (string) $credit->getId()]);

        $creditNotes = $this->buildCreditNotesFromCredit($credit);
        $creditNote = $this->createCreditNote($creditNotes);

        $this->logger->info('Credit note registered with Xero', ['tenant_id' => $this->tenantId, 'credit_id' => (string) $credit->getId()]);

        return new CreditRegistration($creditNote->getCreditNoteID());
    }

    private function createCreditNote(CreditNotes $creditNotes): CreditNote
    {
        $this->logger->info('Creating credit note in xero');

        try {
            $response = $this->accountingApi->createCreditNotes($this->tenantId, $creditNotes);
        } catch (\Exception $e) {
            $this->getLogger()->error('Error while creating credit note in xero', ['exception_message' => $e->getMessage()]);

            throw new UnexpectedErrorException('Error creating credit note in xero', previous: $e);
        }

        $creditNote = $response->getCreditNotes()[0];

        if ($creditNote->getHasErrors()) {
            $this->getLogger()->error('Validation error while creating credit note in xero', ['errors' => $creditNote->getValidationErrors()]);

            throw new UnexpectedErrorException('Error creating credit note in xero');
        }

        $this->logger->info('Credit note created in xero');

        return $creditNote;
    }

    private function buildCreditNotesFromRefund(Refund $refund): CreditNotes
    {
        $contact = new Contact();
        $contact->setContactId($refund->getCustomer()->getAccountingReference());

        $line = new LineItem();
        $line->setAccountCode($this->accountCode);
        $line->setDescription($refund->getReason());
        $line->setQuantity(1);
        $line->setUnitAmount((string) $refund->getAsMoney()->getAmount());

        $creditNote = new CreditNote();
        $creditNote->setType(CreditNote::TYPE_ACCPAYCREDIT);
        $creditNote->setContact($contact);
        $creditNote->setDate($refund->getCreatedAt()->format('Y-m-d'));
        $creditNote->setLineItems([$line]);
        $creditNote->setStatus(CreditNote::STATUS_AUTHORISED);

        $creditNotes = new CreditNotes();
        $creditNotes->setCreditNotes([$creditNote]);

        return $creditNotes;
    }

    private function buildCreditNotesFromCredit(Credit $credit): CreditNotes
    {
        $contact = new Contact();
        $contact->setContactId($credit->getCustomer()->getAccountingReference());

        $line = new LineItem();
        $line->setAccountCode($this->accountCode);
        $line->setDescription('Credit');
        $line->setQuantity(1);
        $line->setUnitAmount((string) $credit->getAmountAsMoney()->getAmount());

        $creditNote = new CreditNote();
        $creditNote->setType(CreditNote::TYPE_ACCPAYCREDIT);
        $creditNote->setContact($contact);
        $creditNote->setDate($credit->getCreatedAt()->format('Y-m-d'));
        $creditNote->setLineItems([$line]);
        $creditNote->setStatus(CreditNote::STATUS_AUTHORISED);

        $creditNotes = new CreditNotes();
        $creditNotes->setCreditNotes([$creditNote]);

        return $creditNotes;
    }
}

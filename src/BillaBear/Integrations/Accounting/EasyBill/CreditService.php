<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\EasyBill;

use BillaBear\Entity\Credit;
use BillaBear\Entity\Refund;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Accounting\CreditRegistration;
use BillaBear\Integrations\Accounting\CreditServiceInterface;
use BillaBear\Integrations\Accounting\RefundRegistration;
use easybill\SDK\Client;
use Parthenon\Common\LoggerAwareTrait;

class CreditService implements CreditServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(private Client $client)
    {
    }

    public function registerRefund(Refund $refund): RefundRegistration
    {
        $this->logger->info('Registering refund with EasyBill', ['refund_id' => (string) $refund->getId()]);
        $creditNote = $this->buildCreditNoteFromRefund($refund);
        try {
            $response = $this->client->request('POST', 'documents', $creditNote);
        } catch (\Exception $e) {
            $this->logger->error('Failed to register credit note refund to EasyBill', ['refund_id' => (string) $refund->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to register credit note refund to EasyBill', previous: $e);
        }

        $this->client->request('PUT', 'documents/'.$response['id'].'/done');
        try {
            $body = $this->buildPaymentData($response['id'], $refund);
            $response = $this->client->request('POST', 'document-payments?paid=true', $body);
        } catch (\Exception $e) {
            $this->logger->error('Failed to register refund to EasyBill', ['refund_id' => (string) $refund->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to register refund to EasyBill', previous: $e);
        }

        $this->logger->info('Refund registered to EasyBill', ['refund_id' => (string) $refund->getId()]);

        return new RefundRegistration((string) $response['id']);
    }

    public function registeredCreditNote(Credit $credit): CreditRegistration
    {
        $this->logger->info('Registering credit note with EasyBill', ['credit_id' => (string) $credit->getId()]);
        $creditNote = $this->buildCreditNoteFromCredit($credit);
        try {
            $response = $this->client->request('POST', 'documents', $creditNote);
        } catch (\Exception $e) {
            $this->logger->error('Failed to register credit note to EasyBill', ['credit_id' => (string) $credit->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to register credit note to EasyBill', previous: $e);
        }

        $this->logger->info('Credit note registered to EasyBill', ['credit_id' => (string) $credit->getId()]);

        return new CreditRegistration((string) $response['id']);
    }

    private function buildCreditNoteFromRefund(Refund $refund): array
    {
        $lines = [
            'description' => $refund->getReason() ?? 'Refund',
            'quantity' => 1,
            'total_gross_price' => $refund->getAmount(),
        ];

        return [
            'type' => 'CREDIT_NOTE',
            'currency' => $refund->getCurrency(),
            'customer_id' => $refund->getCustomer()->getAccountingReference(),
            'created_at' => $refund->getCreatedAt()->format('Y-m-d H:i:s'),
            'calc_vat_from' => 0,
            'due_date' => null,
            'items' => $lines,
            'is_draft' => false,
            'status' => 'DONE',
        ];
    }

    private function buildPaymentData(int $documentNumber, Refund $refund): array
    {
        return [
            'document_id' => $documentNumber,
            'reference' => $refund->getExternalReference(),
            'payment_at' => $refund->getCreatedAt()->format('Y-m-d'),
            'amount' => $refund->getAmount(),
        ];
    }

    private function buildCreditNoteFromCredit(Credit $credit)
    {
        $lines = [
            'description' => $credit->getReason() ?? 'Credit',
            'quantity' => 1,
            'total_gross_price' => $credit->getAmount(),
        ];

        return [
            'type' => 'CREDIT_NOTE',
            'currency' => $credit->getCurrency(),
            'customer_id' => $credit->getCustomer()->getAccountingReference(),
            'created_at' => $credit->getCreatedAt()->format('Y-m-d H:i:s'),
            'calc_vat_from' => 0,
            'due_date' => null,
            'items' => $lines,
            'is_draft' => false,
            'status' => 'DONE',
        ];
    }
}

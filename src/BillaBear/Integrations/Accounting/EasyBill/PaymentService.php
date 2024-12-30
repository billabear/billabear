<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\EasyBill;

use BillaBear\Entity\Payment;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Accounting\PaymentRegistration;
use BillaBear\Integrations\Accounting\PaymentServiceInterface;
use easybill\SDK\Client;
use Parthenon\Common\LoggerAwareTrait;

class PaymentService implements PaymentServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(private Client $client)
    {
    }

    public function register(Payment $payment): PaymentRegistration
    {
        $body = $this->buildPaymentData($payment);
        $this->getLogger()->info('Registering payment with EasyBill', ['payment_id' => (string) $payment->getId(), 'body' => $body]);
        try {
            $response = $this->client->request('POST', 'document-payments?paid=true', $body);
        } catch (\Exception $e) {
            $this->getLogger()->error('Failed to register payment to EasyBill', ['payment_id' => (string) $payment->getId(), 'error' => $e->getMessage()]);

            throw new UnexpectedErrorException('Failed to register payment to EasyBill', previous: $e);
        }

        $this->getLogger()->info('Payment registered to EasyBill', ['payment_id' => (string) $payment->getId()]);

        return new PaymentRegistration((string) $response['id']);
    }

    private function buildPaymentData(Payment $payment): array
    {
        return [
            'document_id' => $payment->getInvoice()->getAccountingReference(),
            'provider' => $payment->getProvider(),
            'reference' => $payment->getPaymentReference(),
            'payment_at' => $payment->getCreatedAt()->format('Y-m-d'),
            'amount' => $payment->getAmount(),
        ];
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Xero;

use BillaBear\Entity\Payment;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\Accounting\PaymentRegistration;
use BillaBear\Integrations\Accounting\PaymentServiceInterface;
use GuzzleHttp\ClientInterface;
use Parthenon\Common\LoggerAwareTrait;
use XeroAPI\XeroPHP\Api\AccountingApi;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Models\Accounting\Account;
use XeroAPI\XeroPHP\Models\Accounting\Invoice;
use XeroAPI\XeroPHP\Models\Accounting\Payment as XeroPayment;
use XeroAPI\XeroPHP\Models\Accounting\Payments;

class PaymentService implements PaymentServiceInterface
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

    public function register(Payment $payment): PaymentRegistration
    {
        $payments = $this->buildPayment($payment);
        $this->getLogger()->info('Registering payment to xero', ['tenant_id' => $this->tenantId, 'payment_id' => (string) $payment->getId()]);
        try {
            $output = $this->accountingApi->createPayments($this->tenantId, $payments);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create payment to xero', ['exception_message' => $e->getMessage()]);
            throw new UnexpectedErrorException($e->getMessage(), previous: $e);
        }

        /** @var XeroPayment $paymentData */
        $paymentData = $output->getPayments()[0];

        if ($paymentData->getHasValidationErrors()) {
            $this->logger->error('Failed to create payment to xero due to validation errors', ['tenant_id' => $this->tenantId, 'payment_id' => (string) $payment->getId(), 'validation_errors' => $paymentData->getValidationErrors()]);
            throw new UnexpectedErrorException('Failed to create payment to xero due to validation errors');
        }

        $this->getLogger()->info('Payment registered to xero', ['tenant_id' => $this->tenantId, 'payment_id' => (string) $payment->getId(), 'accounting_reference' => $paymentData->getPaymentId()]);

        return new PaymentRegistration($paymentData->getPaymentId());
    }

    protected function buildPayment(Payment $payment): Payments
    {
        $xeroInvoice = new Invoice();
        $xeroInvoice->setInvoiceId($payment->getInvoice()->getAccountingReference());

        $account = new Account();
        $account->setCode($this->accountCode);

        $xeroPayment = new XeroPayment();
        $xeroPayment->setInvoice($xeroInvoice);
        $xeroPayment->setAccount($account);
        $xeroPayment->setAmount((string) $payment->getMoneyAmount()->getAmount());
        $xeroPayment->setDate($payment->getCreatedAt()->format('Y-m-d'));

        $payments = new Payments();
        $payments->setPayments([$xeroPayment]);

        return $payments;
    }
}

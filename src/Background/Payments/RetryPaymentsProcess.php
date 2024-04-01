<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\Payments;

use App\Payment\InvoiceCharger;
use App\Repository\PaymentFailureProcessRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class RetryPaymentsProcess
{
    use LoggerAwareTrait;

    public function __construct(
        private PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        private InvoiceCharger $invoiceCharger,
    ) {
    }

    public function execute(): void
    {
        $this->getLogger()->info('Starting payment retries');

        $processes = $this->paymentFailureProcessRepository->findRetriesForNextMinute();

        foreach ($processes as $paymentFailureProcess) {
            $invoice = $paymentFailureProcess->getPaymentAttempt()->getInvoice();

            if (!$invoice) {
                // If there is no invoice then Stripe Billing is handling it.
                // Todo solve when stripe billing is disabled
                continue;
            }

            $this->invoiceCharger->chargeInvoice($invoice);
        }

        $this->getLogger()->info('Finished payment retries');
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Payments;

use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\PaymentFailureProcessRepositoryInterface;
use Obol\Exception\PaymentFailureException;
use Parthenon\Common\LoggerAwareTrait;

class RetryPaymentsProcess
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        private readonly InvoiceCharger $invoiceCharger,
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

            try {
                $this->invoiceCharger->chargeInvoice($invoice);
            } catch (PaymentFailureException) {
            }
        }

        $this->getLogger()->info('Finished payment retries');
    }
}

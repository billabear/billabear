<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

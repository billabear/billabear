<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Payment;

use App\Entity\Invoice;
use App\Entity\PaymentFailureProcess;
use App\Event\PaymentFailed;
use App\Factory\PaymentAttemptFactory;
use App\Repository\PaymentAttemptRepositoryInterface;
use App\Repository\PaymentFailureProcessRepositoryInterface;
use Obol\Model\ChargeCardResponse;
use Obol\Model\Events\ChargeFailed;
use Parthenon\Billing\Entity\Payment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentFailureHandler
{
    public function __construct(
        private PaymentAttemptFactory $paymentAttemptFactory,
        private PaymentAttemptRepositoryInterface $paymentAttemptRepository,
        private PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function handleInvoiceAndResponse(Invoice $invoice, ChargeCardResponse $cardResponse): void
    {
        $paymentAttempt = $this->paymentAttemptFactory->createFromInvoice($invoice, $cardResponse->getChargeFailure()->getReason());
        $this->paymentAttemptRepository->save($paymentAttempt);

        $this->process($paymentAttempt);
    }

    public function handlePayment(Payment $payment, ChargeFailed $chargeFailed): void
    {
        $paymentAttempt = $this->paymentAttemptFactory->createFromPayment($payment, $chargeFailed->getReason());
        $this->paymentAttemptRepository->save($paymentAttempt);

        $this->process($paymentAttempt);
    }

    protected function process(\App\Entity\PaymentAttempt $paymentAttempt): void
    {
        $paymentFailureProcess = $this->paymentFailureProcessRepository->findActiveForCustomer($paymentAttempt->getCustomer());

        if (!$paymentFailureProcess) {
            $paymentFailureProcess = new PaymentFailureProcess();
            $paymentFailureProcess->setState('started');
            $paymentFailureProcess->setResolved(false);
            $paymentFailureProcess->setRetryCount(0);
            $paymentFailureProcess->setCustomer($paymentAttempt->getCustomer());
            $paymentFailureProcess->setPaymentAttempt($paymentAttempt);
            $paymentFailureProcess->setCreatedAt(new \DateTime('now'));
            $paymentFailureProcess->setUpdatedAt(new \DateTime('now'));
            $paymentFailureProcess->setNextAttemptAt(new \DateTime(PaymentFailureProcess::DEFAULT_NEXT_ATTEMPT));
        } else {
            $paymentFailureProcess->increaseRetryCount();
        }

        $paymentFailureProcess->setNextAttemptAt(new \DateTime(PaymentFailureProcess::DEFAULT_NEXT_ATTEMPT));
        $this->paymentFailureProcessRepository->save($paymentFailureProcess);

        $this->dispatcher->dispatch(new PaymentFailed($paymentAttempt, $paymentFailureProcess), PaymentFailed::NAME);
    }
}

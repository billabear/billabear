<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment;

use BillaBear\DataMappers\PaymentAttemptDataMapper;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\PaymentAttempt;
use BillaBear\Entity\PaymentFailureProcess;
use BillaBear\Event\Payment\PaymentFailed;
use BillaBear\Repository\PaymentAttemptRepositoryInterface;
use BillaBear\Repository\PaymentFailureProcessRepositoryInterface;
use Obol\Model\Enum\ChargeFailureReasons;
use Obol\Model\Events\ChargeFailed;
use Parthenon\Billing\Entity\Payment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class PaymentFailureHandler
{
    public function __construct(
        private PaymentAttemptDataMapper $paymentAttemptFactory,
        private PaymentAttemptRepositoryInterface $paymentAttemptRepository,
        private PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        private EventDispatcherInterface $dispatcher,
        private WorkflowInterface $paymentFailureProcessStateMachine,
    ) {
    }

    public function handleInvoiceAndResponse(Invoice $invoice, ChargeFailureReasons|string $chargeFailureReasons): void
    {
        $paymentAttempt = $this->paymentAttemptFactory->createFromInvoice($invoice, $chargeFailureReasons);
        $this->paymentAttemptRepository->save($paymentAttempt);

        $this->process($paymentAttempt);
    }

    public function handlePayment(Payment $payment, ChargeFailed $chargeFailed): void
    {
        $paymentAttempt = $this->paymentAttemptFactory->createFromPayment($payment, $chargeFailed->getReason());
        $this->paymentAttemptRepository->save($paymentAttempt);

        $this->process($paymentAttempt);
    }

    protected function process(PaymentAttempt $paymentAttempt): void
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

        if (5 === $paymentFailureProcess->getRetryCount()) {
            if ($this->paymentFailureProcessStateMachine->can($paymentFailureProcess, 'retries_failed')) {
                $this->paymentFailureProcessStateMachine->apply($paymentFailureProcess, 'retries_failed');
            }
            $paymentFailureProcess->setState('payment_failure_no_more_retries');
        } else {
            $paymentFailureProcess->setNextAttemptAt(new \DateTime(PaymentFailureProcess::DEFAULT_NEXT_ATTEMPT));
        }

        $this->paymentFailureProcessRepository->save($paymentFailureProcess);

        $this->dispatcher->dispatch(new PaymentFailed($paymentAttempt, $paymentFailureProcess), PaymentFailed::NAME);
    }
}

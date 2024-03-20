<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\Payment;

use App\Entity\PaymentCreation;
use Parthenon\Billing\Receipt\ReceiptGeneratorInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class GenerateReceiptTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ReceiptGeneratorInterface $receiptGenerator,
        private ReceiptRepositoryInterface $receiptRepository,
    ) {
    }

    public function transition(Event $event)
    {
        $this->getLogger()->info('Starting create receipt');

        /** @var PaymentCreation $paymentCreation */
        $paymentCreation = $event->getSubject();
        $payment = $paymentCreation->getPayment();
        if (!$payment->getCustomer()) {
            return;
        }

        $receipt = $this->receiptGenerator->generateReceiptForPayment($payment);
        $this->receiptRepository->save($receipt);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_payment.transition.create_receipt' => ['transition'],
        ];
    }
}

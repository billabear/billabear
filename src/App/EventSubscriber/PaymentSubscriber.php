<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\EventSubscriber;

use App\Entity\PaymentCreation;
use App\Payment\PaymentCreationProcessor;
use App\Repository\PaymentCreationRepositoryInterface;
use Parthenon\Billing\Event\PaymentCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PaymentCreationRepositoryInterface $paymentCreationRepository,
        private PaymentCreationProcessor $paymentCreationProcessor,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            PaymentCreated::NAME => [
                'handleNewPayment',
            ],
        ];
    }

    public function handleNewPayment(PaymentCreated $paymentCreated)
    {
        $payment = $paymentCreated->getPayment();

        $paymentCreation = new PaymentCreation();
        $paymentCreation->setPayment($payment);
        $paymentCreation->setCreatedAt(new \DateTime('now'));
        $paymentCreation->setState('started');

        $this->paymentCreationRepository->save($paymentCreation);
        $this->paymentCreationProcessor->process($paymentCreation);
    }
}

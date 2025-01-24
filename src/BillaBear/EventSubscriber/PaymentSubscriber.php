<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber;

use BillaBear\Entity\PaymentCreation;
use BillaBear\Payment\PaymentCreationProcessor;
use BillaBear\Repository\PaymentCreationRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ProcessPaymentCreated;
use Parthenon\Billing\Event\PaymentCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PaymentCreationRepositoryInterface $paymentCreationRepository,
        private PaymentCreationProcessor $paymentCreationProcessor,
        private MessageBusInterface $messageBus,
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
        $this->messageBus->dispatch(new ProcessPaymentCreated((string) $paymentCreation->getId()));
    }
}

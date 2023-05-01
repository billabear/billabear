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
<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\EventSubscriber;

use App\Entity\RefundCreatedProcess;
use App\Payment\RefundCreatedProcessor;
use App\Repository\RefundCreatedProcessRepositoryInterface;
use Parthenon\Billing\Event\RefundCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RefundSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
        private RefundCreatedProcessor $refundCreatedProcessor,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            RefundCreated::NAME => [
                'handleNewPayment',
            ],
        ];
    }

    public function handleNewPayment(RefundCreated $refundCreated)
    {
        $refund = $refundCreated->getRefund();

        $paymentCreation = new RefundCreatedProcess();
        $paymentCreation->setRefund($refund);
        $paymentCreation->setCreatedAt(new \DateTime('now'));
        $paymentCreation->setUpdatedAt(new \DateTime('now'));
        $paymentCreation->setState('started');

        $this->refundCreatedProcessRepository->save($paymentCreation);
        $this->refundCreatedProcessor->process($paymentCreation);
    }
}

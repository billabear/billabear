<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber;

use BillaBear\Entity\RefundCreatedProcess;
use BillaBear\Payment\RefundCreatedProcessor;
use BillaBear\Repository\RefundCreatedProcessRepositoryInterface;
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

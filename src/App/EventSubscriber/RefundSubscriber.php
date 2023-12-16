<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

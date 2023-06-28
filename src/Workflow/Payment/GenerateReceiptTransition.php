<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\Payment;

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

        $receipt = $this->receiptGenerator->generateReceiptForPayment($payment);
        $this->receiptRepository->save($receipt);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.payment_creation.transition.create_receipt' => ['transition'],
        ];
    }
}

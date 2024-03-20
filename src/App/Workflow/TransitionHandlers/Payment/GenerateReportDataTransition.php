<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\Payment;

use App\Entity\PaymentCreation;
use App\Stats\PaymentAmountStats;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class GenerateReportDataTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private PaymentAmountStats $amountStats,
    ) {
    }

    public function transition(Event $event)
    {
        $paymentCreation = $event->getSubject();

        if (!$paymentCreation instanceof PaymentCreation) {
            $this->getLogger()->error('Payment creation transition has something other than a PaymentCreated object', ['class' => get_class($paymentCreation)]);

            return;
        }
        $this->amountStats->process($paymentCreation->getPayment());

        $this->getLogger()->info('Payment stats generated');
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_payment.transition.generate_report_data' => ['transition'],
        ];
    }
}

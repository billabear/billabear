<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
            'workflow.payment_creation.transition.generate_report_data' => ['transition'],
        ];
    }
}

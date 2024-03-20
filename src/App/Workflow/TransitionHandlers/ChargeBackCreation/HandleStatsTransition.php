<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\ChargeBackCreation;

use App\Entity\ChargeBackCreation;
use App\Stats\ChargeBackAmountStats;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class HandleStatsTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ChargeBackAmountStats $amountStats,
    ) {
    }

    public function transition(Event $event)
    {
        $chargeBackCreation = $event->getSubject();

        if (!$chargeBackCreation instanceof ChargeBackCreation) {
            $this->getLogger()->error('Refund creation transition has something other than a PaymentCreated object', ['class' => get_class($chargeBackCreation)]);

            return;
        }
        $this->amountStats->process($chargeBackCreation->getChargeBack());

        $this->getLogger()->info('Payment stats generated');
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_chargeback.transition.handle_stats' => ['transition'],
        ];
    }
}

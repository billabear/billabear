<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\ChargeBackCreation;

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
            'workflow.charge_back_creation.transition.handle_stats' => ['transition'],
        ];
    }
}

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

namespace App\Workflow\PaymentFailure;

use App\Entity\PaymentFailureProcess;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class RetriesFailed implements EventSubscriberInterface
{
    public function __construct(private SubscriptionManagerInterface $subscriptionManager)
    {
    }

    public function transition(Event $event)
    {
        /** @var PaymentFailureProcess $paymentFailureProcess */
        $paymentFailureProcess = $event->getSubject();
        foreach ($paymentFailureProcess->getPaymentAttempt()->getSubscriptions() as $subscription) {
            $this->subscriptionManager->cancelSubscriptionInstantly($subscription);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.payment_failure_process.transition.retries_failed' => ['transition'],
        ];
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\TransitionHandlers\PaymentFailure;

use App\Entity\CancellationRequest;
use App\Entity\PaymentFailureProcess;
use App\Enum\CancellationType;
use App\Repository\CancellationRequestRepositoryInterface;
use App\Subscription\CancellationRequestProcessor;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class RetriesFailed implements EventSubscriberInterface
{
    public function __construct(
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
        private CancellationRequestProcessor $requestProcessor,
        private SubscriptionManagerInterface $subscriptionManager)
    {
    }

    public function transition(Event $event)
    {
        /** @var PaymentFailureProcess $paymentFailureProcess */
        $paymentFailureProcess = $event->getSubject();
        foreach ($paymentFailureProcess->getPaymentAttempt()->getSubscriptions() as $subscription) {
            $requestCancellation = new CancellationRequest();
            $requestCancellation->setCancellationType(CancellationType::BILLING_RELATED);
            $requestCancellation->setSubscription($subscription);
            $requestCancellation->setState('started');
            $requestCancellation->setRefundType('none');
            $requestCancellation->setWhen('instantly');
            $requestCancellation->setOriginalValidUntil($subscription->getValidUntil());
            $requestCancellation->setCreatedAt(new \DateTime());

            $this->cancellationRequestRepository->save($requestCancellation);
            $this->requestProcessor->process($requestCancellation);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.payment_failure_process.transition.retries_failed' => ['transition'],
        ];
    }
}

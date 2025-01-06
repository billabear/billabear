<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\PaymentFailure;

use BillaBear\Entity\CancellationRequest;
use BillaBear\Entity\PaymentFailureProcess;
use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Subscription\CancellationRequestProcessor;
use BillaBear\Subscription\CancellationType;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class RetriesFailed implements EventSubscriberInterface
{
    public function __construct(
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
        private CancellationRequestProcessor $requestProcessor,
    ) {
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

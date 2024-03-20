<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\SubscriptionCancel;

use App\Dto\Request\App\CancelSubscription;
use App\Entity\CancellationRequest;
use App\Entity\Customer;
use Parthenon\Billing\Refund\RefundManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class IssueRefundTransition implements EventSubscriberInterface
{
    public function __construct(private RefundManagerInterface $refundManager)
    {
    }

    public function transition(Event $event)
    {
        /** @var CancellationRequest $cancellationRequest */
        $cancellationRequest = $event->getSubject();
        $subscription = $cancellationRequest->getSubscription();
        $user = $cancellationRequest->getBillingAdmin();
        /** @var Customer $customer */
        $customer = $subscription->getCustomer();
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return;
        }

        if (CancelSubscription::REFUND_PRORATE === $cancellationRequest->getRefundType()) {
            $newValidUntil = $subscription->getValidUntil();
            $this->refundManager->issueProrateRefundForSubscription($subscription, $user, $cancellationRequest->getOriginalValidUntil(), $newValidUntil);
        } elseif (CancelSubscription::REFUND_FULL === $cancellationRequest->getRefundType()) {
            $this->refundManager->issueFullRefundForSubscription($subscription, $user);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.cancel_subscription.transition.issue_refund' => ['transition'],
        ];
    }
}

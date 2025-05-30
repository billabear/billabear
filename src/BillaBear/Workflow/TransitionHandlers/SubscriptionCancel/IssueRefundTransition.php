<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\SubscriptionCancel;

use BillaBear\Dto\Request\App\CancelSubscription;
use BillaBear\Entity\CancellationRequest;
use BillaBear\Entity\Customer;
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

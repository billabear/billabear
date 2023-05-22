<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\SubscriptionCancel;

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
            'workflow.cancellation_request.transition.issue_refund' => ['transition'],
        ];
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrailEnded;

use BillaBear\Entity\Processes\TrialEndedProcess;
use BillaBear\Webhook\Outbound\Payload\Subscription\TrialEndedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcher;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendCustomerNoticeTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private WebhookDispatcher $eventDispatcher,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var TrialEndedProcess $trialEnded */
        $trialEnded = $event->getSubject();
        $subscription = $trialEnded->getSubscription();
        $payload = new TrialEndedPayload($subscription);
        $this->eventDispatcher->dispatch($payload);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.trial_ended.transition.send_customer_notice' => ['transition'],
        ];
    }
}

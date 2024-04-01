<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription;

use App\Entity\SubscriptionCreation;
use App\Repository\SubscriptionCreationRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class SubscriptionCreationProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['handle_stats', 'send_customer_notice', 'send_internal_notice'];

    public function __construct(
        private WorkflowInterface $subscriptionCreationStateMachine,
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
    ) {
    }

    public function process(SubscriptionCreation $request): void
    {
        $stateMachine = $this->subscriptionCreationStateMachine;

        $request->setHasError(false);
        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($stateMachine->can($request, $transition)) {
                    $stateMachine->apply($request, $transition);

                    $this->getLogger()->info('Did subscription creation transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do subscription creation transition", ['transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Subscription Creation transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $request->setError($e->getMessage());
            $request->setHasError(true);
        }

        $this->subscriptionCreationRepository->save($request);
    }
}

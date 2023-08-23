<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
        $atateMachine = $this->subscriptionCreationStateMachine;

        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($atateMachine->can($request, $transition)) {
                    $atateMachine->apply($request, $transition);

                    $this->getLogger()->info('Did subscription creation transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do subscription creation transition", ['transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Subscription Creation transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $request->setError($e->getMessage());
        }

        $this->subscriptionCreationRepository->save($request);
    }
}

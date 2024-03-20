<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription;

use App\Entity\SubscriptionCreation;
use App\Enum\WorkflowType;
use App\Repository\SubscriptionCreationRepositoryInterface;
use App\Workflow\WorkflowProcessor;
use Parthenon\Common\LoggerAwareTrait;

class SubscriptionCreationProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['handle_stats', 'send_customer_notice', 'send_internal_notice'];

    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
    ) {
    }

    public function process(SubscriptionCreation $request): void
    {
        $this->workflowProcessor->process($request, WorkflowType::CREATE_SUBSCRIPTION, $this->subscriptionCreationRepository);
    }
}

<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Repository\SubscriptionCreationRepositoryInterface;
use BillaBear\Subscription\Process\SubscriptionCreationProcessor;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedSubscriptionCreated;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReprocessFailedSubscriptionCreatedHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        private SubscriptionCreationProcessor $creationProcessor,
    ) {
    }

    public function __invoke(ReprocessFailedSubscriptionCreated $created)
    {
        $this->getLogger()->info('Started to process failed subscription creations');
        $failed = $this->subscriptionCreationRepository->getFailedCreations();

        foreach ($failed as $request) {
            $this->getLogger()->info('Processing a failed subscription creation', ['subscription_creation' => (string) $request->getId()]);
            $this->creationProcessor->process($request);
        }
    }
}

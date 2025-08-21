<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Repository\SubscriptionCreationRepositoryInterface;
use BillaBear\Subscription\Process\SubscriptionCreationProcessor;
use BillaBear\Workflow\Messenger\Messages\ProcessSubscriptionCreated;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessSubscriptionCreatedHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        private SubscriptionCreationProcessor $creationProcessor,
    ) {
    }

    public function __invoke(ProcessSubscriptionCreated $created): void
    {
        $this->getLogger()->info('Started to process subscription creation', ['subscription_creation_id' => $created->id]);
        $process = $this->subscriptionCreationRepository->findById($created->id);
        $this->creationProcessor->process($process);
    }
}

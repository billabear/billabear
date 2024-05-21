<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Payment\ChargeBackCreationProcessor;
use BillaBear\Repository\ChargeBackCreationRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ReprocessFailedChargeBackCreation;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReprocessFailedChargeBackCreationHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        private ChargeBackCreationProcessor $chargeBackCreationProcessor,
    ) {
    }

    public function __invoke(ReprocessFailedChargeBackCreation $command)
    {
        $this->getLogger()->info('Started to process failed charge back creations');
        $failed = $this->chargeBackCreationRepository->getFailedProcesses();

        foreach ($failed as $request) {
            $this->getLogger()->info('Processing a failed charge back creation', ['charge_back_creation' => (string) $request->getId()]);
            $this->chargeBackCreationProcessor->process($request);
        }
    }
}

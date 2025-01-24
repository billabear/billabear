<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Messenger\Handlers;

use BillaBear\Payment\ChargeBackCreationProcessor;
use BillaBear\Repository\ChargeBackCreationRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ProcessChargeBack;
use Parthenon\Common\LoggerAwareTrait;

class ProcessChargeBackHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        private ChargeBackCreationProcessor $chargeBackCreationProcessor,
    ) {
    }

    public function __invoke(ProcessChargeBack $processChargeBack)
    {
        $this->getLogger()->info('Started to process charge back creation', ['charge_back_creation_id' => $processChargeBack->id]);
        $process = $this->chargeBackCreationRepository->findById($processChargeBack->id);
        $this->chargeBackCreationProcessor->process($process);
    }
}

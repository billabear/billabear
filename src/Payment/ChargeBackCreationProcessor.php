<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Payment;

use App\Entity\ChargeBackCreation;
use App\Repository\ChargeBackCreationRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class ChargeBackCreationProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['handle_stats', 'send_customer_notice', 'send_internal_notice'];

    public function __construct(
        private WorkflowInterface $chargeBackCreationStateMachine,
        private ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
    ) {
    }

    public function process(ChargeBackCreation $chargeBackCreation): void
    {
        $chargeBackCreationStateMachine = $this->chargeBackCreationStateMachine;

        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($chargeBackCreationStateMachine->can($chargeBackCreation, $transition)) {
                    $chargeBackCreationStateMachine->apply($chargeBackCreation, $transition);

                    $this->getLogger()->info('Did charge back creation transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do charge back creation transition", ['transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Charge back creation transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $chargeBackCreation->setError($e->getMessage());
        }

        $this->chargeBackCreationRepository->save($chargeBackCreation);
    }
}

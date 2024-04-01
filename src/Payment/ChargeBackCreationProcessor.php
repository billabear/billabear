<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

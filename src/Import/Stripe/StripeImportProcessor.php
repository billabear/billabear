<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Import\Stripe;

use App\Repository\StripeImportRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class StripeImportProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['start', 'start_customers', 'start_products', 'start_prices', 'start_subscriptions', 'start_payments', 'start_refunds', 'start_charge_backs'];

    public function __construct(
        private WorkflowInterface $stripeImportStateMachine,
        private StripeImportRepositoryInterface $stripeImportRepository,
    ) {
    }

    public function process(): void
    {
        $request = $this->stripeImportRepository->findActive();

        if (!$request) {
            return;
        }

        $fiveMinutesAgo = new \DateTime('-1 minutes');

        if ($request->getUpdatedAt() > $fiveMinutesAgo && 'started' !== $request->getState()) {
            $this->getLogger()->info('Waiting 5 minutes incase last process is still running');
            // Wait 5 minutes to restart a failed import process
            return;
        }

        $attempts = $request->getAttempts();
        ++$attempts;
        $request->setAttempts($attempts);

        $stripeImportStateMachine = $this->stripeImportStateMachine;

        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($stripeImportStateMachine->can($request, $transition)) {
                    $stripeImportStateMachine->apply($request, $transition);

                    $this->getLogger()->info('Did stripe import transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do stripe import transition", ['transition' => $transition]);
                }
                $this->stripeImportRepository->save($request);
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Cancellation stripe import failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $request->setError($e->getMessage());
            if (5 === $attempts) {
                $this->getLogger()->warning('Import has failed 5 times marking complete');
                $request->setComplete(true);
            }
        }

        $this->stripeImportRepository->save($request);
    }
}
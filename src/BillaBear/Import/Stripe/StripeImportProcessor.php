<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\Notification\Email\Email;
use BillaBear\Notification\Email\EmailSenderFactoryInterface;
use BillaBear\Repository\StripeImportRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Workflow\WorkflowInterface;

#[Autoconfigure(lazy: true)]
class StripeImportProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['start', 'start_customers', 'start_products', 'start_prices', 'start_subscriptions', 'start_payments', 'start_refunds', 'start_charge_backs', 'crunch_stats'];

    public function __construct(
        private WorkflowInterface $stripeImportStateMachine,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private EmailSenderFactoryInterface $emailSenderFactory,
    ) {
    }

    public function process(): void
    {
        $request = $this->stripeImportRepository->findActive();

        if (!$request) {
            return;
        }
        $this->emailSenderFactory->disable();

        $fiveMinutesAgo = new \DateTime('-2 minutes');

        if ($request->getUpdatedAt() > $fiveMinutesAgo && 'started' !== $request->getState()) {
            $this->getLogger()->info('Waiting 2 minutes incase last process is still running');

            // Wait 5 minutes to restart a failed import process
            return;
        }

        $attempts = $request->getAttempts();
        ++$attempts;
        $request->setAttempts($attempts);

        $stripeImportStateMachine = $this->stripeImportStateMachine;
        $request->setError(null);

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
            if ('completed' === $request->getState()) {
                $request->setComplete(true);
                $this->emailSenderFactory->enable();

                if ($request->getCreatedBy()) {
                    $failureEmail = new Email();
                    $failureEmail->setTemplateName('Mail/Import/Stripe/success.html.twig');
                    $failureEmail->setToAddress($request->getCreatedBy()->getEmail());

                    $emailSender = $this->emailSenderFactory->create();
                    $emailSender->send($failureEmail);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Cancellation stripe import failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $request->setError($e->getMessage());
            if (5 === $attempts) {
                $this->getLogger()->warning('Import has failed 5 times marking complete');
                $request->setComplete(true);
                $request->setState('failed');
                $this->emailSenderFactory->enable();

                if ($request->getCreatedBy()) {
                    $failureEmail = new Email();
                    $failureEmail->setTemplateName('Mail/Import/Stripe/failure.html.twig');
                    $failureEmail->setTemplateVariables(['errorMessage' => $request->getError()]);
                    $failureEmail->setToAddress($request->getCreatedBy()->getEmail());

                    $emailSender = $this->emailSenderFactory->create();
                    $emailSender->send($failureEmail);
                }
            }
        }

        $this->stripeImportRepository->save($request);
    }
}

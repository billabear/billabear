<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\ExpiringCards;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Processes\ExpiringCardProcess;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use BillaBear\Repository\Processes\ExpiringCardProcessRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;
use Symfony\Component\Workflow\WorkflowInterface;

readonly class StartProcess
{
    public function __construct(
        private PaymentCardRepositoryInterface $paymentCardRepository,
        private ExpiringCardProcessRepositoryInterface $expiringCardProcessRepository,
        private WorkflowInterface $expiringCardProcessStateMachine,
    ) {
    }

    public function execute(): void
    {
        $cards = $this->paymentCardRepository->getExpiringDefaultThisMonth();

        foreach ($cards as $paymentCard) {
            $expiringCardProcess = new ExpiringCardProcess();
            $expiringCardProcess->setState('started');
            $expiringCardProcess->setCustomer($paymentCard->getCustomer());
            $expiringCardProcess->setPaymentCard($paymentCard);
            $expiringCardProcess->setCreatedAt(new \DateTime('now'));
            $expiringCardProcess->setUpdatedAt(new \DateTime('now'));

            /** @var Customer $customer */
            $customer = $paymentCard->getCustomer();
            $nextChargeAt = null;
            /** @var Subscription $subscription */
            foreach ($customer->getSubscriptions() as $subscription) {
                $subscription->getValidUntil();
                if (!$nextChargeAt) {
                    $nextChargeAt = $subscription->getValidUntil();
                }
                if ($subscription->getValidUntil() < $nextChargeAt) {
                    $nextChargeAt = $subscription->getValidUntil();
                }
            }
            $expiringCardProcess->setSubscriptionChargedAt($nextChargeAt);

            $this->expiringCardProcessRepository->save($expiringCardProcess);

            $this->expiringCardProcessStateMachine->apply($expiringCardProcess, 'send_first_email');
            $this->expiringCardProcessRepository->save($expiringCardProcess);
        }
    }
}

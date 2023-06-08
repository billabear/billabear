<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Background\ExpiringCards;

use App\Entity\Customer;
use App\Entity\Processes\ExpiringCardProcess;
use App\Repository\PaymentCardRepositoryInterface;
use App\Repository\Processes\ExpiringCardProcessRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;
use Symfony\Component\Workflow\WorkflowInterface;

class StartProcess
{
    public function __construct(
        private PaymentCardRepositoryInterface $paymentCardRepository,
        private ExpiringCardProcessRepositoryInterface $expiringCardProcessRepository,
        private WorkflowInterface $expiringCardProcessStateMachine,
    ) {
    }

    public function execute()
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

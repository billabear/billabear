<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\EventSubscriber;

use App\Repository\Processes\ExpiringCardProcessRepositoryInterface;
use Parthenon\Billing\Event\PaymentCardAdded;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class PaymentCardSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ExpiringCardProcessRepositoryInterface $expiringCardProcessRepository,
        private WorkflowInterface $expiringCardProcessStateMachine,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            \Parthenon\Billing\Event\PaymentCardAdded::NAME => [
                'handleNewPayment',
            ],
        ];
    }

    public function handleNewPayment(PaymentCardAdded $paymentCreated)
    {
        try {
            $expiringCard = $this->expiringCardProcessRepository->getActiveProcessForCustomer($paymentCreated->getCustomer());
        } catch (NoEntityFoundException $e) {
            return;
        }

        if ($this->expiringCardProcessStateMachine->can($expiringCard, 'handle_card_added')) {
            $this->getLogger()->info('Finishing the card expiring process');
            $this->expiringCardProcessStateMachine->apply($expiringCard, 'handle_card_added');
        }
        $this->expiringCardProcessRepository->save($expiringCard);
    }
}

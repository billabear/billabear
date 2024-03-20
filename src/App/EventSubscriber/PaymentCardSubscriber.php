<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
            PaymentCardAdded::NAME => [
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

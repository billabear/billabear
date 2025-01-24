<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber;

use BillaBear\Entity\ChargeBackCreation;
use BillaBear\Repository\ChargeBackCreationRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ProcessChargeBack;
use Parthenon\Billing\Event\ChargeBackCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ChargeBackSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            ChargeBackCreated::NAME => [
                'handleNewPayment',
            ],
        ];
    }

    public function handleNewPayment(ChargeBackCreated $chargeBackCreated)
    {
        $chargeBack = $chargeBackCreated->getChargeBack();

        $paymentCreation = new ChargeBackCreation();
        $paymentCreation->setChargeBack($chargeBack);
        $paymentCreation->setCreatedAt(new \DateTime('now'));
        $paymentCreation->setState('started');

        $this->chargeBackCreationRepository->save($paymentCreation);
        $this->messageBus->dispatch(new ProcessChargeBack((string) $paymentCreation->getId()));
    }
}

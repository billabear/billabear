<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\EventSubscriber;

use App\Entity\ChargeBackCreation;
use App\Payment\ChargeBackCreationProcessor;
use App\Repository\ChargeBackCreationRepositoryInterface;
use Parthenon\Billing\Event\ChargeBackCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChargeBackSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
        private ChargeBackCreationProcessor $chargeBackCreationProcessor,
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
        $this->chargeBackCreationProcessor->process($paymentCreation);
    }
}

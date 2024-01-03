<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

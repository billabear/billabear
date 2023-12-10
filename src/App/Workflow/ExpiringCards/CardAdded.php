<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\ExpiringCards;

use App\Entity\Processes\ExpiringCardProcess;
use App\Entity\Voucher;
use App\Enum\VoucherEvent;
use App\Repository\VoucherRepositoryInterface;
use App\Voucher\VoucherApplier;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class CardAdded implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private VoucherRepositoryInterface $voucherRepository,
        private VoucherApplier $voucherApplier,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var ExpiringCardProcess $process */
        $process = $event->getSubject();

        try {
            $voucher = $this->voucherRepository->getActiveByEvent(VoucherEvent::EXPIRED_CARD_ADDED);
            if ($voucher instanceof Voucher) {
                $this->voucherApplier->applyVoucherToCustomer($process->getCustomer(), $voucher);
            }
        } catch (NoEntityFoundException $e) {
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.expiring_card_process.transition.handle_card_added' => ['transition'],
        ];
    }
}

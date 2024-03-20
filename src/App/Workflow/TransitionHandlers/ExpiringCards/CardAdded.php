<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\ExpiringCards;

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

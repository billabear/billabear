<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\ExpiringCards;

use BillaBear\Entity\Processes\ExpiringCardProcess;
use BillaBear\Entity\Voucher;
use BillaBear\Repository\VoucherRepositoryInterface;
use BillaBear\Voucher\VoucherApplier;
use BillaBear\Voucher\VoucherEvent;
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

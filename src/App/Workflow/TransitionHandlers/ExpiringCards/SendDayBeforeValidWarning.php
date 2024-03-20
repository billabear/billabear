<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\ExpiringCards;

use App\Entity\Processes\ExpiringCardProcess;
use App\Enum\VoucherEvent;
use App\Notification\Email\Data\ExpiringCardBeforeCharge;
use App\Notification\Email\EmailBuilder;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\VoucherRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class SendDayBeforeValidWarning implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private EmailSenderInterface $emailSender,
        private EmailBuilder $builder,
        private VoucherRepositoryInterface $voucherRepository,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var ExpiringCardProcess $process */
        $process = $event->getSubject();

        if (!$process->getCustomer()->getBrandSettings()->getNotificationSettings()->getExpiringCardDayBefore()) {
            $this->getLogger()->info('Brand has expiring card warning day before email disable');

            return;
        }

        if (!isset($event->getContext()['subscription'])) {
            throw new \Exception('Subscription not set');
        }

        $subscription = $event->getContext()['subscription'];

        if (!$subscription instanceof Subscription) {
            throw new \Exception('Subscription is not a subscription');
        }
        $voucher = $this->voucherRepository->getActiveByEvent(VoucherEvent::EXPIRED_CARD_ADDED);
        $emailData = new ExpiringCardBeforeCharge($process->getPaymentCard(), $subscription, $voucher);
        $email = $this->builder->build($process->getCustomer(), $emailData);
        $this->emailSender->send($email);
        $this->getLogger()->info('Sent customer notice', ['sender' => get_class($this->emailSender)]);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.expiring_card_process.transition.send_day_before_valid_email' => ['transition'],
        ];
    }
}

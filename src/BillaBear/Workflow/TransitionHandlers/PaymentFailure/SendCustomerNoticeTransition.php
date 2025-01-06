<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\PaymentFailure;

use BillaBear\Entity\PaymentFailureProcess;
use BillaBear\Notification\Email\Data\PaymentFailureEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendCustomerNoticeTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private EmailSenderInterface $emailSender,
        private EmailBuilder $builder,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var PaymentFailureProcess $paymentFailureProcess */
        $paymentFailureProcess = $event->getSubject();
        $paymentAttempt = $paymentFailureProcess->getPaymentAttempt();
        $customer = $paymentFailureProcess->getCustomer();

        if (!$customer->getBrandSettings()->getNotificationSettings()->getPaymentFailure()) {
            $this->getLogger()->info('Brand does not have payment failure email');

            return;
        }

        $this->getLogger()->info('Starting customer notice transition');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getNotificationSettings()?->getSendCustomerNotifications()) {
            $this->getLogger()->info('Sending customer notifications are disabled in the settings');

            return;
        }

        $emailData = new PaymentFailureEmail($paymentAttempt);
        $email = $this->builder->build($customer, $emailData);
        $this->emailSender->send($email);
        $this->getLogger()->info('Sent customer notice', ['sender' => get_class($this->emailSender)]);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.payment_failure_process.transition.send_customer_notice' => ['transition'],
        ];
    }
}

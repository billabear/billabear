<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Payment;

use BillaBear\Entity\PaymentCreation;
use BillaBear\Notification\Email\Data\PaymentCreatedEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Pdf\ReceiptPdfGenerator;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Attachment;
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
        private ReceiptRepositoryInterface $receiptRepository,
        private EmailSenderInterface $emailSender,
        private EmailBuilder $builder,
        private ReceiptPdfGenerator $pdfGenerator,
    ) {
    }

    public function transition(Event $event)
    {
        $paymentCreation = $event->getSubject();

        if (!$paymentCreation instanceof PaymentCreation) {
            $this->getLogger()->error('Payment creation transition has something other than a PaymentCreated object', ['class' => get_class($paymentCreation)]);

            return;
        }

        $this->getLogger()->info('Starting customer notice transition');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getNotificationSettings()?->getSendCustomerNotifications()) {
            $this->getLogger()->info('Sending customer notifications are disabled in the settings');

            return;
        }
        $receipt = $this->receiptRepository->getForPayment($paymentCreation->getPayment());

        $emailData = new PaymentCreatedEmail($paymentCreation->getPayment(), $receipt[0]);

        $pdf = $this->pdfGenerator->generate($receipt[0]);
        $attachment = new Attachment('receipt.pdf', $pdf);

        $email = $this->builder->build($paymentCreation->getPayment()->getCustomer(), $emailData);
        $email->addAttachment($attachment);
        $this->emailSender->send($email);
        $this->getLogger()->info('Sent customer notice', ['sender' => get_class($this->emailSender)]);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_payment.transition.send_customer_notice' => ['transition'],
        ];
    }
}

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

namespace App\Workflow\TransitionHandlers\Payment;

use App\Entity\PaymentCreation;
use App\Notification\Email\Data\PaymentCreatedEmail;
use App\Notification\Email\EmailBuilder;
use App\Pdf\ReceiptPdfGenerator;
use App\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

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
            $this->getLogger()->info('Starting customer notifications are disabled in the settings');

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
            'workflow.payment_creation.transition.send_customer_notice' => ['transition'],
        ];
    }
}

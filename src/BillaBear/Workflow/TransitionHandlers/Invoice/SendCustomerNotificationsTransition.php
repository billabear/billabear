<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Invoice;

use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Invoice\Formatter\InvoicePdfGenerator;
use BillaBear\Invoice\PayLinkGeneratorInterface;
use BillaBear\Notification\Email\Data\InvoiceCreatedEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendCustomerNotificationsTransition implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private PayLinkGeneratorInterface $payLinkGenerator,
        private InvoicePdfGenerator $invoicePdfGenerator,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.invoice_process.transition.send_customer_notifications' => ['transition'],
        ];
    }

    public function transition(Event $event)
    {
        /** @var InvoiceProcess $invoiceProcess */
        $invoiceProcess = $event->getSubject();
        $invoice = $invoiceProcess->getInvoice();
        $customer = $invoice->getCustomer();
        $brand = $customer->getBrandSettings();

        if (!$brand->getNotificationSettings()->getInvoiceCreated()) {
            return;
        }
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getNotificationSettings()?->getSendCustomerNotifications()) {
            $this->getLogger()->info('Sending customer notifications are disabled in the settings');

            return;
        }
        $fullPayLink = $this->payLinkGenerator->generatePayLink($invoice);

        $pdf = $this->invoicePdfGenerator->generate($invoice);
        $attachment = new Attachment(sprintf('invoice-%s.pdf', $invoice->getInvoiceNumber()), $pdf);

        $invoiceCreatedEmail = new InvoiceCreatedEmail($invoice, $fullPayLink);
        $email = $this->emailBuilder->build($customer, $invoiceCreatedEmail);
        $email->addAttachment($attachment);
        $this->emailSender->send($email);
    }
}

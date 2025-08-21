<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Invoice;

use BillaBear\Checkout\PayLinkGeneratorInterface;
use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use BillaBear\Notification\Email\Data\InvoiceOverdueEmail;
use BillaBear\Notification\Email\EmailBuilder;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendOverdueNotificationsTransition implements EventSubscriberInterface
{
    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private PayLinkGeneratorInterface $payLinkGenerator,
        private InvoiceFormatterProvider $invoiceFormatterProvider,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.invoice_process.transition.send_customer_warning' => ['transition'],
        ];
    }

    public function transition(Event $event)
    {
        /** @var InvoiceProcess $invoiceProcess */
        $invoiceProcess = $event->getSubject();
        $invoice = $invoiceProcess->getInvoice();
        $customer = $invoice->getCustomer();
        $brand = $customer->getBrandSettings();

        if (!$brand->getNotificationSettings()->getInvoiceOverdue()) {
            return;
        }

        $fullPayLink = $this->payLinkGenerator->generatePayLink($invoice);

        $generator = $this->invoiceFormatterProvider->getFormatter($invoice->getCustomer());
        $pdf = $generator->generate($invoice);
        $filename = $generator->filename($invoice);
        $attachment = new Attachment($filename, $pdf);

        $invoiceOverdueEmail = new InvoiceOverdueEmail($invoice, $fullPayLink);
        $email = $this->emailBuilder->build($customer, $invoiceOverdueEmail);
        $email->addAttachment($attachment);
        $this->emailSender->send($email);
    }
}

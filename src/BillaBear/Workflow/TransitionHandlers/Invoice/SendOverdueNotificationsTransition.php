<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Invoice;

use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Invoice\PayLinkGenerator;
use BillaBear\Notification\Email\Data\InvoiceOverdueEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Pdf\InvoicePdfGenerator;
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
        private PayLinkGenerator $payLinkGenerator,
        private InvoicePdfGenerator $invoicePdfGenerator,
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

        $pdf = $this->invoicePdfGenerator->generate($invoice);
        $attachment = new Attachment(sprintf('invoice-%s.pdf', $invoice->getInvoiceNumber()), $pdf);

        $invoiceOverdueEmail = new InvoiceOverdueEmail($invoice, $fullPayLink);
        $email = $this->emailBuilder->build($customer, $invoiceOverdueEmail);
        $email->addAttachment($attachment);
        $this->emailSender->send($email);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\Invoice;

use App\Entity\Processes\InvoiceProcess;
use App\Invoice\PayLinkGenerator;
use App\Notification\Email\Data\InvoiceOverdueEmail;
use App\Notification\Email\EmailBuilder;
use App\Pdf\InvoicePdfGenerator;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

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

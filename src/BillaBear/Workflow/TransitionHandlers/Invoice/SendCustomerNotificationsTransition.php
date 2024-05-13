<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\Invoice;

use BillaBear\Entity\Processes\InvoiceProcess;
use BillaBear\Invoice\PayLinkGenerator;
use BillaBear\Notification\Email\Data\InvoiceCreatedEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Pdf\InvoicePdfGenerator;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

#[Autoconfigure(lazy: true)]
class SendCustomerNotificationsTransition implements EventSubscriberInterface
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

        $fullPayLink = $this->payLinkGenerator->generatePayLink($invoice);

        $pdf = $this->invoicePdfGenerator->generate($invoice);
        $attachment = new Attachment(sprintf('invoice-%s.pdf', $invoice->getInvoiceNumber()), $pdf);

        $invoiceCreatedEmail = new InvoiceCreatedEmail($invoice, $fullPayLink);
        $email = $this->emailBuilder->build($customer, $invoiceCreatedEmail);
        $this->emailSender->send($email);
    }
}

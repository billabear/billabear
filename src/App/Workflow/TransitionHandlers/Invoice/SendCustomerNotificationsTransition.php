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

namespace App\Workflow\TransitionHandlers\Invoice;

use App\Entity\Processes\InvoiceProcess;
use App\Invoice\PayLinkGenerator;
use App\Notification\Email\Data\InvoiceCreatedEmail;
use App\Notification\Email\EmailBuilder;
use App\Pdf\InvoicePdfGenerator;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

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

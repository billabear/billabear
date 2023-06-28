<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\EventSubscriber;

use App\Event\InvoiceCreated;
use App\Notification\Email\Data\InvoiceCreatedEmail;
use App\Notification\Email\EmailBuilder;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoiceCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            InvoiceCreated::NAME => [
                'handleNewInvoice',
            ],
        ];
    }

    public function handleNewInvoice(InvoiceCreated $created)
    {
        $invoice = $created->getInvoice();
        $customer = $invoice->getCustomer();
        $brand = $customer->getBrandSettings();

        if (!$brand->getNotificationSettings()->getInvoiceCreated()) {
            return;
        }

        $invoiceCreatedEmail = new InvoiceCreatedEmail($invoice);
        $email = $this->emailBuilder->build($customer, $invoiceCreatedEmail);
        $this->emailSender->send($email);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\EventSubscriber;

use App\Event\InvoiceCreated;
use App\Notification\Email\Data\InvoiceCreatedEmail;
use App\Notification\Email\EmailBuilder;
use App\Repository\SettingsRepositoryInterface;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvoiceCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private UrlGeneratorInterface $urlGenerator,
        private SettingsRepositoryInterface $settingsRepository,
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

        $payLink = $this->urlGenerator->generate('app_public_invoice_readpay', ['hash' => $invoice->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
        $fullPayLink = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getSystemUrl().$payLink;

        $invoiceCreatedEmail = new InvoiceCreatedEmail($invoice, $fullPayLink);
        $email = $this->emailBuilder->build($customer, $invoiceCreatedEmail);
        $this->emailSender->send($email);
    }
}

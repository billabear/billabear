<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery;

use BillaBear\Checkout\PayLinkGeneratorInterface;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceDeliverySettings;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use BillaBear\Notification\Email\Data\InvoiceCreatedEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;

class EmailDeliveryHandler implements DeliveryHandlerInterface
{
    use LoggerAwareTrait;

    public const NAME = 'email';

    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private PayLinkGeneratorInterface $payLinkGenerator,
        private InvoiceFormatterProvider $invoiceFormatterProvider,
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function deliver(Invoice $invoice, InvoiceDeliverySettings $invoiceDelivery): void
    {
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

        $generator = $this->invoiceFormatterProvider->getFormatterByType($invoiceDelivery->getInvoiceFormat());
        $pdf = $generator->generate($invoice);
        $filename = $generator->filename($invoice);
        $attachment = new Attachment($filename, $pdf);

        $invoiceCreatedEmail = new InvoiceCreatedEmail($invoice, $fullPayLink);
        $email = $this->emailBuilder->build($customer, $invoiceCreatedEmail);
        if ($invoiceDelivery->getEmail()) {
            $email->setToAddress($invoiceDelivery->getEmail());
        }
        $email->addAttachment($attachment);
        $this->emailSender->send($email);
    }
}

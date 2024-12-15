<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber;

use BillaBear\Event\Quote\QuoteCreated;
use BillaBear\Notification\Email\Data\QuoteCreatedEmail;
use BillaBear\Notification\Email\EmailBuilder;
use BillaBear\Pdf\QuotePdfGenerator;
use BillaBear\Quotes\PayLinkGeneratorInterface;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class QuoteCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private QuotePdfGenerator $pdfGenerator,
        private PayLinkGeneratorInterface $payLinkGenerator,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            QuoteCreated::NAME => [
                'handleNewQuote',
            ],
        ];
    }

    public function handleNewQuote(QuoteCreated $created)
    {
        $quote = $created->quote;
        $customer = $quote->getCustomer();
        $brand = $customer->getBrandSettings();

        if (!$brand->getNotificationSettings()->getQuoteCreated()) {
            return;
        }

        $fullPayLink = $this->payLinkGenerator->generatePayLink($quote);
        $quoteCreatedEmail = new QuoteCreatedEmail($quote, $fullPayLink);
        $email = $this->emailBuilder->build($customer, $quoteCreatedEmail);

        $pdf = $this->pdfGenerator->generate($quote);
        $attachment = new Attachment(sprintf('quote-%s.pdf', $quote->getId()), $pdf);
        $email->addAttachment($attachment);
        $this->emailSender->send($email);
    }
}

<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\EventSubscriber;

use App\Event\QuoteCreated;
use App\Notification\Email\Data\QuoteCreatedEmail;
use App\Notification\Email\EmailBuilder;
use App\Pdf\QuotePdfGenerator;
use App\Quotes\PayLinkGenerator;
use Parthenon\Notification\Attachment;
use Parthenon\Notification\EmailSenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class QuoteCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private QuotePdfGenerator $pdfGenerator,
        private PayLinkGenerator $payLinkGenerator,
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
        $quote = $created->getQuote();
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

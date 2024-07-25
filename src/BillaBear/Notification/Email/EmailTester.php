<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email;

use BillaBear\Dummy\Data\ReceiptProvider;
use BillaBear\Dummy\Data\SubscriptionProvider;
use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Entity\User;
use BillaBear\Notification\Email\Data\DayBeforeChargeWarningEmail;
use BillaBear\Notification\Email\Data\ExpiringCardBeforeCharge;
use BillaBear\Notification\Email\Data\ExpiringCardBeforeChargeNotValid;
use BillaBear\Notification\Email\Data\ExpiringCardEmai;
use BillaBear\Notification\Email\Data\InvoiceCreatedEmail;
use BillaBear\Notification\Email\Data\InvoiceOverdueEmail;
use BillaBear\Notification\Email\Data\PaymentCreatedEmail;
use BillaBear\Notification\Email\Data\QuoteCreatedEmail;
use BillaBear\Notification\Email\Data\SubscriptionCancelEmail;
use BillaBear\Notification\Email\Data\SubscriptionCreatedEmailData;
use BillaBear\Notification\Email\Data\TrialEndingWarningEmail;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use Parthenon\Notification\Email;
use Parthenon\Notification\EmailSenderInterface;

class EmailTester
{
    public function __construct(
        private SubscriptionProvider $subscriptionProvider,
        private ReceiptProvider $receiptProvider,
        private EmailBuilder $emailBuilder,
        private EmailSenderInterface $emailSender,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
    ) {
    }

    public function sendTest(User $user, EmailTemplate $template)
    {
        $email = match ($template->getName()) {
            EmailTemplate::NAME_SUBSCRIPTION_RENEWAL_WARNING => $this->createDayBeforeCharge($user, $template),
            EmailTemplate::NAME_PAYMENT_METHOD_DAY_BEFORE_WARNING => $this->createExpiredCardBefore($user, $template),
            EmailTemplate::NAME_PAYMENT_METHOD_DAY_BEFORE_NOT_VALID_WARNING => $this->createExpiredCardBeforeNotValid($user, $template),
            EmailTemplate::NAME_PAYMENT_METHOD_EXPIRY_WARNING => $this->createExpiredCard($user, $template),
            EmailTemplate::NAME_INVOICE_CREATED => $this->createInvoiceCreated($user, $template),
            EmailTemplate::NAME_INVOICE_OVERDUE => $this->createInvoiceOverdue($user, $template),
            EmailTemplate::NAME_SUBSCRIPTION_CANCELLED => $this->createSubscriptionCancelled($user, $template),
            EmailTemplate::NAME_TRIAL_ENDING_WARNING => $this->createDayBeforeTrialEndingWarning($user, $template),
            EmailTemplate::NAME_SUBSCRIPTION_CREATED => $this->createSubscriptionCreated($user, $template),
        };

        $this->emailSender->send($email);
    }

    private function createSubscriptionCreated(User $user, EmailTemplate $template): Email
    {
        $subscription = $this->subscriptionProvider->createSubscription();
        $subscription->getCustomer()->setBillingEmail($user->getEmail());
        $subscription->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new SubscriptionCreatedEmailData($subscription);

        return $this->emailBuilder->buildWithTemplate($subscription->getCustomer(), $template, $emailData);
    }

    private function createSubscriptionCancelled(User $user, EmailTemplate $template): Email
    {
        $subscription = $this->subscriptionProvider->createSubscription();
        $subscription->getCustomer()->setBillingEmail($user->getEmail());
        $subscription->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new SubscriptionCancelEmail($subscription);

        return $this->emailBuilder->buildWithTemplate($subscription->getCustomer(), $template, $emailData);
    }

    private function createDayBeforeCharge(User $user, EmailTemplate $template): Email
    {
        $subscription = $this->subscriptionProvider->createSubscription();
        $subscription->getCustomer()->setBillingEmail($user->getEmail());
        $subscription->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new DayBeforeChargeWarningEmail($subscription);

        return $this->emailBuilder->buildWithTemplate($subscription->getCustomer(), $template, $emailData);
    }

    private function createDayBeforeTrialEndingWarning(User $user, EmailTemplate $template): Email
    {
        $subscription = $this->subscriptionProvider->createSubscription();
        $subscription->getCustomer()->setBillingEmail($user->getEmail());
        $subscription->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new TrialEndingWarningEmail($subscription);

        return $this->emailBuilder->buildWithTemplate($subscription->getCustomer(), $template, $emailData);
    }

    private function createInvoiceCreated(User $user, EmailTemplate $template): Email
    {
        $invoice = $this->receiptProvider->getInvoice();
        $invoice->getCustomer()->setBillingEmail($user->getEmail());
        $invoice->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new InvoiceCreatedEmail($invoice, 'http://example.org');

        return $this->emailBuilder->buildWithTemplate($invoice->getCustomer(), $template, $emailData);
    }

    private function createInvoiceOverdue(User $user, EmailTemplate $template): Email
    {
        $invoice = $this->receiptProvider->getInvoice();
        $invoice->getCustomer()->setBillingEmail($user->getEmail());
        $invoice->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new InvoiceOverdueEmail($invoice, 'http://example.org');

        return $this->emailBuilder->buildWithTemplate($invoice->getCustomer(), $template, $emailData);
    }

    private function createPaymentCreated(User $user, EmailTemplate $template): Email
    {
        $payment = $this->receiptProvider->getDummyPayment();
        $receipt = $this->receiptProvider->getDummyReceipt();
        $receipt->getCustomer()->setBillingEmail($user->getEmail());
        $receipt->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new PaymentCreatedEmail($payment, $receipt);

        return $this->emailBuilder->buildWithTemplate($receipt->getCustomer(), $template, $emailData);
    }

    private function createQuoteCreated(User $user, EmailTemplate $template): Email
    {
        $quote = $this->receiptProvider->getQuote();
        $quote->getCustomer()->setBillingEmail($user->getEmail());
        $quote->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $emailData = new QuoteCreatedEmail($quote, 'hds');

        return $this->emailBuilder->buildWithTemplate($quote->getCustomer(), $template, $emailData);
    }

    private function createExpiredCardBefore(User $user, EmailTemplate $template): Email
    {
        $subscription = $this->subscriptionProvider->createSubscription();
        $subscription->getCustomer()->setBillingEmail($user->getEmail());
        $subscription->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $paymentCard = $this->receiptProvider->getPaymentCard();

        $emailData = new ExpiringCardBeforeCharge($paymentCard, $subscription);

        return $this->emailBuilder->buildWithTemplate($subscription->getCustomer(), $template, $emailData);
    }

    private function createExpiredCardBeforeNotValid(User $user, EmailTemplate $template): Email
    {
        $subscription = $this->subscriptionProvider->createSubscription();
        $subscription->getCustomer()->setBillingEmail($user->getEmail());
        $subscription->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $paymentCard = $this->receiptProvider->getPaymentCard();

        $emailData = new ExpiringCardBeforeChargeNotValid($paymentCard, $subscription);

        return $this->emailBuilder->buildWithTemplate($subscription->getCustomer(), $template, $emailData);
    }

    private function createExpiredCard(User $user, EmailTemplate $template): Email
    {
        $subscription = $this->subscriptionProvider->createSubscription();
        $subscription->getCustomer()->setBillingEmail($user->getEmail());
        $subscription->getCustomer()->setBrandSettings($this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND));
        $paymentCard = $this->receiptProvider->getPaymentCard();

        $emailData = new ExpiringCardEmai($paymentCard, $subscription);

        return $this->emailBuilder->buildWithTemplate($subscription->getCustomer(), $template, $emailData);
    }
}

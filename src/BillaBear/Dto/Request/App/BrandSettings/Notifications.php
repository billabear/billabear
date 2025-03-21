<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\BrandSettings;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class Notifications
{
    #[SerializedName('subscription_creation')]
    private $subscriptionCreation;

    #[SerializedName('subscription_cancellation')]
    private $subscriptionCancellation;

    #[SerializedName('expiring_card_warning')]
    private $expiringCardWarning;

    #[SerializedName('expiring_card_warning_day_before')]
    private $expiringCardDayBeforeWarning;

    #[SerializedName('invoice_created')]
    private $invoiceCreated;

    #[SerializedName('invoice_overdue')]
    private $invoiceOverdue;

    #[SerializedName('quote_created')]
    private $quoteCreated;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('boolean')]
    #[SerializedName('trial_ending_warning')]
    private $trialEndingWarnings;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('boolean')]
    #[SerializedName('payment_failure')]
    private $paymentFailure;

    #[Assert\Choice(choices: ['none', 'all', 'yearly'])]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('before_charge_warning')]
    private $beforeChargeWarnings;

    public function getSubscriptionCreation()
    {
        return true === $this->subscriptionCreation;
    }

    public function setSubscriptionCreation($subscriptionCreation): void
    {
        $this->subscriptionCreation = $subscriptionCreation;
    }

    public function getSubscriptionCancellation()
    {
        return true === $this->subscriptionCancellation;
    }

    public function setSubscriptionCancellation($subscriptionCancellation): void
    {
        $this->subscriptionCancellation = $subscriptionCancellation;
    }

    public function getExpiringCardWarning()
    {
        return true === $this->expiringCardWarning;
    }

    public function setExpiringCardWarning($expiringCardWarning): void
    {
        $this->expiringCardWarning = $expiringCardWarning;
    }

    public function getExpiringCardDayBeforeWarning()
    {
        return true === $this->expiringCardDayBeforeWarning;
    }

    public function setExpiringCardDayBeforeWarning($expiringCardDayBeforeWarning): void
    {
        $this->expiringCardDayBeforeWarning = $expiringCardDayBeforeWarning;
    }

    public function getInvoiceCreated()
    {
        return $this->invoiceCreated;
    }

    public function setInvoiceCreated($invoiceCreated): void
    {
        $this->invoiceCreated = $invoiceCreated;
    }

    public function getQuoteCreated()
    {
        return $this->quoteCreated;
    }

    public function setQuoteCreated($quoteCreated): void
    {
        $this->quoteCreated = $quoteCreated;
    }

    public function getInvoiceOverdue()
    {
        return true === $this->invoiceOverdue;
    }

    public function setInvoiceOverdue($invoiceOverdue): void
    {
        $this->invoiceOverdue = $invoiceOverdue;
    }

    public function getTrialEndingWarnings()
    {
        return $this->trialEndingWarnings;
    }

    public function setTrialEndingWarnings($trialEndingWarnings): void
    {
        $this->trialEndingWarnings = $trialEndingWarnings;
    }

    public function getBeforeChargeWarnings()
    {
        return $this->beforeChargeWarnings;
    }

    public function setBeforeChargeWarnings($beforeChargeWarnings): void
    {
        $this->beforeChargeWarnings = $beforeChargeWarnings;
    }

    public function getPaymentFailure()
    {
        return $this->paymentFailure;
    }

    public function setPaymentFailure($paymentFailure): void
    {
        $this->paymentFailure = $paymentFailure;
    }
}
